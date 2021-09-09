<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Helper;

use Magento\Catalog\Model\Category;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Cms\Model\GetBlockByIdentifier;
use Magento\Framework\Exception\NoSuchEntityException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $_pageHelper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_cmsPage;


    /**
     * Block factory
     *
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $catalogCategory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $layerResolver;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Cms\Helper\Page $pageHelper
     * @param \Magento\Cms\Model\PageFactory $cmsPage
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Helper\Page $pageHelper,
        \Magento\Cms\Model\PageFactory $cmsPage,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Model\BlockFactory $blockFactory
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->catalogCategory = $catalogCategory;
        $this->_pageHelper = $pageHelper;
        $this->_cmsPage = $cmsPage;
        $this->_categoryRepository = $categoryRepository;
        $this->collectionFactory = $categoryCollectionFactory;
        $this->layerResolver = $layerResolver;
        $this->_blockFactory = $blockFactory;
        $this->_filterProvider = $filterProvider;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->_scopeConfig->getValue($path, $scope);
    }

    public function addCmsPage($node, $pageId)
    {
        $url = $this->_pageHelper->getPageUrl($pageId);
        $node->setUrl($url);
        $node->setIsActive($this->_cmsPage->create()->getId() === $pageId);

        return $url;

    }

    public function getBlockById($blockId, $storeId)
    {
        $html = '';
        if ($blockId) {
                /** @var \Magento\Cms\Model\Block $block */
            $block = $this->_blockFactory->create();
            $block->setStoreId($storeId)->load($blockId);
            if ($block->isActive()) {
                $html = $this->filterContent($block->getContent(), $storeId);
            }
        }
        return $html;
    }

    public function filterContent($content, $storeId)
    {
        return $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($content);
    }



    public function addCategories($node, $storeId, $rootId, $children = true, $fullWidth = false)
    {
        $category = $this->getCategory($rootId);

        if (!$category) return;

        $currentCategory = $this->getCurrentCategory();

        $node->setIsActive($currentCategory->getId() === $rootId);

        if ($category->getLevel() > \Magento\Catalog\Model\Category::TREE_ROOT_ID) {
            $node->setUrl($this->catalogCategory->getCategoryUrl($category));
        }

        if (!$children) return;

        $collection = $this->getCategoryTree($storeId, $category, $fullWidth);
        $mapping = [$rootId => $node];  // use nodes stack to avoid recursion
        foreach ($collection as $category) {
            $categoryParentId = $category->getParentId();
            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();
                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$categoryParentId];


            $categoryNode = new Node(
                $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                ),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );


            $parentCategoryNode->addChild($categoryNode);


            $mapping[$category->getId()] = $categoryNode; //add node in stack

        }
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    private function getCategory($id)
    {
        try {
            return $this->_categoryRepository->get($id);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @param bool $isParentActive
     * @return array
     */
    private function getCategoryAsArray($category, $currentCategory, $isParentActive)
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'has_active' => in_array((string)$category->getId(), explode('/', $currentCategory->getPath()), true),
            'is_active' => $category->getId() == $currentCategory->getId(),
            'is_category' => true,
            'is_parent_active' => $isParentActive
        ];
    }

    /**
     * @param $storeId
     * @param $category
     * @param $fullWidth
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected function getCategoryTree($storeId, $category, $fullWidth)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('name');
        $collection->addFieldToFilter('path', ['like' => $category->getPath() . '/%']);
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);

        if ($fullWidth) {
            $collection->addOrder('children_count', Collection::SORT_ORDER_DESC);
        }

        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }


}

<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Block\Adminhtml\Menu;

use Magento\Framework\Data\Tree\Node;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;
use ForMage\MegaMenu\Model\MenuFactory as CategoryFactory;
use ForMage\MegaMenu\Model\ResourceModel\Menu\Tree as MenuTreeResource;

/**
 * Class Tree
 * @package ForMage\MegaMenu
 */
class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * Tree constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree
     * @param \Magento\Framework\Registry $registry
     * @param CatalogCategoryFactory $categoryFactory
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param CategoryFactory $menuFactory
     * @param MenuTreeResource $menuTree
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        CatalogCategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        CategoryFactory $menuFactory,
        MenuTreeResource $menuTree,
        array $data = []
    ) {

        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $jsonEncoder, $resourceHelper, $backendSession, $data);

        $this->_categoryTree = $menuTree;
        $this->_categoryFactory = $menuFactory;
        $this->_withProductCount = false;
    }

    /**
     * @return string
     */
    public function getNodesUrl()
    {
        return $this->getUrl('megamenu/menu/jsonTree');
    }

    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);

        return $this->getUrl('megamenu/*/save', $params);
    }

    /**
     * @return string
     */
    public function getMoveUrl()
    {
        return $this->getUrl('megamenu/menu/move');
    }

    /**
     * @return string
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            'megamenu/menu/edit',
            ['store' => null, '_query' => false, 'id' => null, 'parent' => null]
        );
    }

    protected function _getNodeJson($node, $level = 0)
    {
        // create a node from data array
        if (is_array($node)) {
            $node = new Node($node, 'entity_id', new \Magento\Framework\Data\Tree());
        }

        $item = [];
        $item['text'] = $this->buildNodeName($node);

        $rootForStores = in_array($node->getEntityId(), $this->getRootIds());

        $item['id'] = $node->getId();
        $item['store'] = (int)$this->getStore()->getId();
        $item['path'] = $node->getData('path');

        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        //$item['allowDrop'] = ($level<3) ? true : false;
        $allowMove = $this->_isCategoryMoveable($node);
        $item['allowDrop'] = $allowMove;
        // disallow drag if it's first level and category is root of a store
        $item['allowDrag'] = $allowMove && ($node->getLevel() == 1 && $rootForStores ? false : true);

        if ((int)$node->getChildrenCount() > 0) {
            $item['children'] = [];
        }

        $isParent = $this->_isParentSelectedCategory($node);

        if ($node->hasChildren()) {
            $item['children'] = [];
            if (!($this->getUseAjax() && $node->getLevel() > 1 && !$isParent)) {
                foreach ($node->getChildren() as $child) {
                    $item['children'][] = $this->_getNodeJson($child, $level + 1);
                }
            }
        }

        if ($isParent || $node->getLevel() < 2) {
            $item['expanded'] = true;
        }

        return $item;
    }

    /**
     * Return ids of root categories as array
     *
     * @return array
     */
    public function getRootIds()
    {
        $ids = $this->getData('root_ids');
        if ($ids === null) {
            $ids = [Category::TREE_ROOT_ID];
            $this->setData('root_ids', $ids);
        }

        return $ids;
    }



}

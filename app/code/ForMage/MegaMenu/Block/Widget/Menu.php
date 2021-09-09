<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use ForMage\MegaMenu\Helper\Data as Helper;

class Menu extends Template implements BlockInterface
{
    /**
     * Cache identities
     *
     * @var array
     */
    protected $identities = [];

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * Top menu data tree
     *
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $_menu;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var TreeFactory
     */
    private $treeFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var \ForMage\MegaMenu\Model\ResourceModel\Menu\Collection
     */
    private $_collectionFactory;

    protected $_storeId;

    /**
     * Menu constructor.
     * @param Template\Context $context
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param Context $httpContext
     * @param Helper $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Context $httpContext,
        Registry $coreRegistry,
        Helper $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \ForMage\MegaMenu\Model\ResourceModel\Menu\CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->storeManager = $storeManager;
        $this->_helper = $helper;
        $this->httpContext = $httpContext;
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;

        $this->_storeId = $storeManager->getStore()->getId();
    }


    /**
     * Get mega menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
    {
        $this->_eventManager->dispatch(
            'page_block_html_megamenu_gethtml_before',
            ['menu' => $this->getMenu(), 'block' => $this, 'request' => $this->getRequest()]
        );

        $this->getMenu()->setOutermostClass($outermostClass);
        $this->getMenu()->setChildrenWrapClass($childrenWrapClass);


        $storeId = $this->storeManager->getStore()->getId();
        $rootId = $this->storeManager->getStore()->getRootCategoryId();

        $collection = $this->getMenuTree();

        if (!count($collection)) {
            $this->_helper->addCategories($this->getMenu(), $storeId, $rootId);
        }

        $mapping = [$this->getMenuId() => $this->getMenu()];  // use nodes stack to avoid recursion
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
                $this->getNodeAsArray(
                    $category,
                    $category->getParentId() == $categoryParentId
                ),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );

            $categoryNode->setFullWidth($category->getFullWidth());
            $categoryNode->setClass($category->getClass());

            switch ($category->getType()) {
                case 'category':

                    $this->_helper->addCategories($categoryNode, $storeId, $category->getTypeId(), $category->getDropDown(), $category->getFullWidth());
                    $parentCategoryNode->addChild($categoryNode);
                    break;
                case 'current_category':

                    $currentCategory = $this->getCurrentCategory();
                    if ($currentCategory && $currentCategory->getId()) {

                        $this->_helper->addCategories($this->getMenu(), $storeId, $currentCategory->getId(), $category->getDropDown());
                    }
                    break;
                case 'cms':

                    $show = $this->_helper->addCmsPage($categoryNode, $category->getTypeId());
                    if (!$show) continue 2;

                    $parentCategoryNode->addChild($categoryNode);

                    break;
                case 'block':

                    $block = $this->_getBlock($categoryNode, $category->getTypeId());
                    if (!$block) continue 2;

                    $categoryNode->setHtml($block);
                    $categoryNode->setHasBlock(1);
                    $parentCategoryNode->addChild($categoryNode);

                    break;
                case 'custom':

                    if ($category->getCustomUrl()) {
                        $categoryNode->setUrl($category->getCustomUrl());
                    }
                    if ($category->getDescription()) {
                        $html = $this->_helper->filterContent($category->getDescription(), $this->_storeId);
                        $categoryNode->setCustom($html);
                    }
                    $parentCategoryNode->addChild($categoryNode);
                    break;
            }


            $mapping[$category->getId()] = $categoryNode; //add node in stack

        }


        $html = $this->_getHtml($this->getMenu(), $childrenWrapClass, $limit);

        $transportObject = new \Magento\Framework\DataObject(['html' => $html]);
        $this->_eventManager->dispatch(
            'page_block_html_megamenu_gethtml_after',
            ['menu' => $this->getMenu(), 'transportObject' => $transportObject]
        );
        $html = $transportObject->getHtml();
        return $html;
    }

    public function getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Count All Subnavigation Items
     *
     * @param \Magento\Backend\Model\Menu $items
     * @return int
     */
    protected function _countItems($items)
    {
        $total = $items->count();
        foreach ($items as $item) {
            /** @var $item \Magento\Backend\Model\Menu\Item */
            if ($item->hasChildren()) {
                $total += $this->_countItems($item->getChildren());
            }
        }
        return $total;
    }

    /**
     * Building Array with Column Brake Stops
     *
     * @param \Magento\Backend\Model\Menu $items
     * @param int $limit
     * @return array|void
     *
     * @todo: Add Depth Level limit, and better logic for columns
     */
    protected function _columnBrake($items, $limit)
    {

        $total = $this->_countItems($items);
        if (!$limit || $total <= $limit) {
            return;
        }

        $result[] = ['total' => $total, 'max' => (int)ceil($total / ceil($total / $limit))];

        $count = 0;
        $firstCol = true;

        foreach ($items as $item) {
            $place = $this->_countItems($item->getChildren()) + 1;
            $count += $place;

            if ($place >= $limit) {
                $colbrake = !$firstCol;
                $count = 0;
            } elseif ($count >= $limit) {
                $colbrake = !$firstCol;
                $count = $place;
            } else {
                $colbrake = false;
            }

            $result[] = ['place' => $place, 'colbrake' => $colbrake];

            $firstCol = false;
        }

        return $result;
    }

    /**
     * Add sub menu HTML code for current menu item
     *
     * @param \Magento\Framework\Data\Tree\Node $child
     * @param string $childLevel
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string HTML code
     */
    protected function _addSubMenu($child, $childLevel, $childrenWrapClass, $limit)
    {
        $html = '';
        if (!$child->hasChildren()) {
            return $html;
        }

        $colStops = null;
        if ($childLevel == 0) {
            $colStops = $this->_columnBrake($child->getChildren(), $limit);
        }

        $html .= '<ul class="level' . $childLevel . ' ' . $childrenWrapClass . '">';


        $html .= $this->_getHtml($child, $childrenWrapClass, $limit, $colStops);


        $html .= '</ul>';

        return $html;
    }

    protected function _getBlock($child, $id)
    {
        $html = '';
        $html .= '<li class="' . $child->getClass() . '">';
        $html .= $this->_helper->getBlockById($id, $this->_storeId);
        $html .= '</li>';

        return $html;
    }

    /**
     * Recursively generates top menu html from data that is specified in $menuTree
     *
     * @param \Magento\Framework\Data\Tree\Node $menuTree
     * @param string $childrenWrapClass
     * @param int $limit
     * @param array $colBrakes
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        $colBrakes = []
    )
    {
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($child->getData('is_parent_active') === false) {
                continue;
            }
            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);

            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $outermostClassCode = ' class="' . $outermostClass . '" ';
                $currentClass = $child->getClass();

                if (empty($currentClass)) {
                    $child->setClass($outermostClass);
                } else {
                    $child->setClass($currentClass . ' ' . $outermostClass);
                }
            }

            if (is_array($colBrakes) && count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }


            $subMenu = $child->getHtml() ?: $this->_addSubMenu($child, $childLevel, $childrenWrapClass, $limit);
            $content = $child->getCustom() ?: '<span>' . $child->getName(). '</span>';

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';
            $html .= '<a href="' . $child->getUrl() . '" ' . $outermostClassCode . '>' .
                $content
                . '</a>' . $subMenu . '</li>';
            $itemPosition++;
            $counter++;
        }

        if (is_array($colBrakes) && count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }

    /**
     * Generates string with all attributes that should be present in menu item element
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return string
     */
    protected function _getRenderedMenuItemAttributes(Node $item)
    {
        $html = '';
        $attributes = $this->_getMenuItemAttributes($item);
        foreach ($attributes as $attributeName => $attributeValue) {
            $html .= ' ' . $attributeName . '="' . str_replace('"', '\"', $attributeValue) . '"';
        }
        return $html;
    }

    /**
     * Returns array of menu item's attributes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemAttributes(Node $item)
    {
        $menuItemClasses = $this->_getMenuItemClasses($item);
        return ['class' => implode(' ', $menuItemClasses)];
    }

    /**
     * Returns array of menu item's classes
     *
     * @param \Magento\Framework\Data\Tree\Node $item
     * @return array
     */
    protected function _getMenuItemClasses(Node $item)
    {
        $classes = [];

        $classes[] = 'level' . $item->getLevel();
        $classes[] = $item->getPositionClass();

        if ($item->getIsCategory()) {
            $classes[] = 'category-item';
        }

        if ($item->getIsFirst()) {
            $classes[] = 'first';
        }

        if ($item->getIsActive()) {
            $classes[] = 'active';
        } elseif ($item->getHasActive()) {
            $classes[] = 'has-active';
        }

        if ($item->getIsLast()) {
            $classes[] = 'last';
        }

        if ($item->getClass()) {
            $classes[] = $item->getClass();
        }

        if ($item->hasChildren()) {
            $classes[] = 'parent';
        }

        if ($item->getFullWidth()) {
            $classes[] = 'full-width';
        }

        if ($item->getHasBlock()) {
            $classes[] = 'block';
        }

        return $classes;
    }

    /**
     * Get menu object.
     *
     * Creates \Magento\Framework\Data\Tree\Node root node object.
     * The creation logic was moved from class constructor into separate method.
     *
     * @return Node
     * @since 100.1.0
     */
    public function getMenu()
    {
        if (!$this->_menu) {
            $this->_menu = $this->nodeFactory->create(
                [
                    'data' => [],
                    'idField' => 'root',
                    'tree' => $this->treeFactory->create()
                ]
            );
        }
        return $this->_menu;
    }

    protected function getMenuTree()
    {
        /** @var \ForMage\MegaMenu\Model\ResourceModel\Menu\Collection $collection */
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('level');
        $collection->addFieldToFilter('path', ['like' => '1/' . $this->getMenuId() . '/%']);
        $collection->addAttributeToFilter('is_active', 1);
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('menu_id', Collection::SORT_ORDER_ASC);


        return $collection;
    }

    private function getNodeAsArray($node, $isParentActive)
    {
        return [
            'name' => $node->getCustomName() ?: $node->getName(),
            'id' => 'menu-node-' . $node->getId(),
            'url' => $node->getCustomUrl() ?: '#',
            'has_active' => false,
            'is_active' => false,
            'is_category' => true,
            'is_parent_active' => $isParentActive
        ];
    }
}
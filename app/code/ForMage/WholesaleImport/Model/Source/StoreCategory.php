<?php

namespace ForMage\WholesaleImport\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class StoreCategory implements OptionSourceInterface
{

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * StoreCategory constructor.
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    )
    {
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];

        foreach ($this->_toArray() as $key => $value) {
            $data[] = [
                'label' => $value,
                'value' => $key
            ];
        }
        return $data;
    }

    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->_categoryFactory->create()->getCollection();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }

    private function _toArray()
    {
        $categories = $this->getCategoryCollection(true, false, false, false);

        $categoryList = [];
        foreach ($categories as $category) {
            $categoryList[$category->getEntityId()] = __($this->_getParentName($category->getPath()) . $category->getName()) . ' (ID: ' . $category->getEntityId() . ')';
        }
        asort($categoryList);
        return $categoryList;
    }

    private function _getParentName($path = '')
    {
        $parentName = '';
        $rootCats = [1, 2];

        $catTree = explode("/", $path);
        // Deleting category itself
        array_pop($catTree);

        if ($catTree && (count($catTree) > count($rootCats))) {
            foreach ($catTree as $catId) {
                if (!in_array($catId, $rootCats)) {
                    $category = $this->_categoryFactory->create()->load($catId);
                    $categoryName = $category->getName();
                    $parentName .= $categoryName . '/';
                }
            }
        }

        return $parentName;
    }
}

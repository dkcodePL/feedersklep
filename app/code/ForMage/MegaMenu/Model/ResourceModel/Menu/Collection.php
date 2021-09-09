<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Catalog\Model\Category as CategoryModel;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ForMage\MegaMenu\Model\Menu', 'ForMage\MegaMenu\Model\ResourceModel\Menu');
    }

    /**
     * @param $field
     * @param null $condition
     * @return $this
     */
    public function addAttributeToFilter($field, $condition = null)
    {
        return $this->addFieldToFilter($field, $condition);
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'entity_id') {
            $field = 'menu_id';
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setProductStoreId($storeId)
    {
        return $this;
    }

    /**
     * @param $count
     * @return $this
     */
    public function setLoadProductCount($count)
    {
        return $this;
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this;
    }

    /**
     * @param $attribute
     * @param bool $joinType
     * @return $this
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        return $this;
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);

        return $countSelect;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'menu_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * add if filter
     *
     * @param $categoryIds
     * @return $this
     */
    public function addIdFilter($categoryIds)
    {
        $condition = '';

        if (is_array($categoryIds)) {
            if (!empty($categoryIds)) {
                $condition = ['in' => $categoryIds];
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = ['in' => $ids];
            }
        }

        if ($condition != '') {
            $this->addFieldToFilter('menu_id', $condition);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function getActive()
    {
        return $this
            ->addFieldToFilter('is_active', 1);
    }

    /**\
     * @return $this
     */
    public function getMenu()
    {
        return $this
            ->getActive()
            ->addFieldToFilter('parent_id', CategoryModel::TREE_ROOT_ID)
            ->setOrder('position', \Magento\Framework\DB\Select::SQL_ASC);
    }




}

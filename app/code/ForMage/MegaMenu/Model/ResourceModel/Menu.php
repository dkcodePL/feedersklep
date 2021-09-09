<?php
/**
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

namespace ForMage\MegaMenu\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

/**
 * Class Menu
 * @package ForMage\MegaMenu
 */
class Menu extends AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var Menu\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Menu constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Menu\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Menu\CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->collectionFactory = $collectionFactory;
        $this->datetime = $dateTime;
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('4mage_menu', 'menu_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /**
         * @var \ForMage\MegaMenu\Model\Menu $object
         */
        parent::_beforeSave($object);


        if (!$object->getChildrenCount()) {
            $object->setChildrenCount(0);
        }

        if ($object->isObjectNew()) {
            if ($object->getPosition() === null) {
                $object->setPosition($this->_getMaxPosition($object->getPath()) + 1);
            }
            $path = explode('/', $object->getPath());
            $level = count($path) - ($object->getId() ? 1 : 0);
            $toUpdateChild = array_diff($path, [$object->getId()]);

            if (!$object->hasPosition()) {
                $object->setPosition($this->_getMaxPosition(implode('/', $toUpdateChild)) + 1);
            }
            if (!$object->hasLevel()) {
                $object->setLevel($level);
            }
            if (!$object->hasParentId() && $level) {
                $object->setParentId($path[$level - 1]);
            }
            if (!$object->getId()) {
                $object->setPath($object->getPath() . '/');
            }

            $object->setCreatedAt($this->datetime->gmtDate());

            $this->getConnection()->update(
                $this->getMainTable(),
                ['children_count' => new \Zend_Db_Expr('children_count+1')],
                ['menu_id IN(?)' => $toUpdateChild]
            );
        }

        if (is_array($object->getStoreIds())) {
            $storeIds = $object->getStoreIds();
            if (in_array(Store::DEFAULT_STORE_ID, $storeIds)) {
                $storeIds = [Store::DEFAULT_STORE_ID];
            }
            $object->setStoreIds(implode(',', $storeIds));
        }

        return $this;
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setStoreIds(explode(',', $object->getStoreIds()));
        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return mixed
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /**
         * Add identifier for new category
         */
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);
        }

        //$this->updateChildrenStore($object);

        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getMainTable(),
                [
                    'path' => $object->getPath()
                ],
                ['menu_id = ?' => $object->getId()]
            );
            $object->unsetData('path_ids');
        }
        return $this;
    }

    /**
     * Update is_active field
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function disableChildren($object)
    {
        if ($object->getId()) {
            $connection = $this->getConnection();
            $connection->update(
                $this->getMainTable(),
                [
                    'is_active' => 0
                ],
                [$connection->quoteIdentifier('path') . ' LIKE ?' => $object->getPath() . '/%']
            );
        }
        return $this;
    }


    protected function getChildren($object)
    {
        $connection = $this->getConnection();
        $bind = ['b_path' => $object->getPath() . '/%'];
        $select = $connection->select()->from(
            $this->getMainTable(),
            ['menu_id']
        )->where(
            $connection->quoteIdentifier('path') . ' LIKE :b_path'
        );

        return $connection->fetchAll($select, $bind);
    }

    // todo
    public function getChildrenCategories($menu, $level = null)
    {
        $children = $this->getChildren($menu);
        if (!count($children)) {
            $children[] = 0;
        }
        $collection = $menu->getCollection();
        $collection
            ->addIdFilter(
                $children
            )->setOrder(
                'position',
                \Magento\Framework\DB\Select::SQL_ASC
            );

        if ($level) {
            $collection->addAttributeToFilter('level', ['eq' => $level]);
        }

        return $collection;
    }

    /**
     * todo
     * @param $path
     * @return int|string
     */
    protected function _getMaxPosition($path)
    {
        $connection = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');
        $level = count(explode('/', $path));
        $bind = ['c_level' => $level, 'c_path' => $path . '/%'];
        $select = $connection->select()->from(
            $this->getTable('catalog_category_entity'),
            'MAX(' . $positionField . ')'
        )->where(
            $connection->quoteIdentifier('path') . ' LIKE :c_path'
        )->where(
            $connection->quoteIdentifier('level') . ' = :c_level'
        );

        $position = $connection->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }
        return $position;
    }

    public function changeParent(
        \ForMage\MegaMenu\Model\Menu $category,
        \ForMage\MegaMenu\Model\Menu $newParent,
        $afterCategoryId = null
    )
    {
        $childrenCount = $this->getChildrenCount($category->getId()) + 1;
        $table = $this->getMainTable();
        $connection = $this->getConnection();
        $levelFiled = $connection->quoteIdentifier('level');
        $pathField = $connection->quoteIdentifier('path');

        if ($category->getParentId() !== $newParent->getId()) {
            /**
             * Decrease children count for all old category parent categories
             */
            $connection->update(
                $table,
                ['children_count' => new \Zend_Db_Expr('children_count - ' . $childrenCount)],
                ['menu_id IN(?)' => $category->getParentIds()]
            );

            /**
             * Increase children count for new category parents
             */
            $connection->update(
                $table,
                ['children_count' => new \Zend_Db_Expr('children_count + ' . $childrenCount)],
                ['menu_id IN(?)' => $newParent->getPathIds()]
            );
        }

        $position = $this->_processPositions($category, $newParent, $afterCategoryId);

        $newPath = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $connection->update(
            $table,
            [
                'path' => new \Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $connection->quote(
                        $category->getPath() . '/'
                    ) . ', ' . $connection->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new \Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition)
            ],
            [$pathField . ' LIKE ?' => $category->getPath() . '/%']
        );
        /**
         * Update moved category data
         */
        $data = [
            'path' => $newPath,
            'level' => $newLevel,
            'position' => $position,
            'parent_id' => $newParent->getId(),
        ];
        $connection->update($table, $data, ['menu_id = ?' => $category->getId()]);

        // Update category object to new data
        $category->addData($data);
        $category->unsetData('path_ids');

        return $this;
    }

    protected function _processPositions($category, $newParent, $afterCategoryId)
    {
        $table = $this->getMainTable();
        $connection = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');

        $bind = ['position' => new \Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            'parent_id = ?' => $category->getParentId(),
            $positionField . ' > ?' => $category->getPosition(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select = $connection->select()->from($table, 'position')->where('menu_id = :menu_id');
            $position = $connection->fetchOne($select, ['menu_id' => $afterCategoryId]);
            $position += 1;
        } else {
            $position = 1;
        }

        $bind = ['position' => new \Zend_Db_Expr($positionField . ' + 1')];
        $where = ['parent_id = ?' => $newParent->getId(), $positionField . ' >= ?' => $position];
        $connection->update($table, $bind, $where);

        return $position;
    }

    /**
     * Get children menus count
     *
     * @param int $menuId
     * @return int
     */
    public function getChildrenCount($menuId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getMainTable(),
            'children_count'
        )->where(
            'menu_id = :menu_id'
        );
        $bind = ['menu_id' => $menuId];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * TODO
     *
     * @param \ForMage\MegaMenu\Model\Menu $menu
     * @return \Magento\Framework\DataObject[]
     */
    public function getParentCategories($menu)
    {
        $pathIds = array_reverse(explode(',', $menu->getPathInStore()));
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $menus = $this->collectionFactory->create();
        return $menus
            ->addAttributeToSelect(
                'name'
            )->addFieldToFilter(
                'menu_id',
                ['in' => $pathIds]
            )->addFieldToFilter(
                'is_active',
                1
            )->load()->getItems();
    }

    /**
     * @param array $data
     */
    public function insertData($data)
    {
        $this->getConnection()
            ->insertMultiple($this->getMainTable(), $data);
    }

}
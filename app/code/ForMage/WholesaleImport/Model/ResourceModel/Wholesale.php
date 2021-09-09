<?php

namespace ForMage\WholesaleImport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Wholesale resource
 */
class Wholesale extends AbstractDb
{
    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('4mage_wholesaleimport_wholesale', 'id');
    }


    /**
     * @param $data
     * @param $table
     */
    public function insertData($data, $table)
    {
        $this->getConnection()
            ->insertMultiple($this->getTable($table), $data);
    }

    /**
     * @param $condition
     * @param $columnData
     * @param $table
     * @return mixed
     */
    public function updateData($condition, $columnData, $table)
    {
        return $this->getConnection()->update(
            $this->getTable($table),
            $columnData,
            $where = $condition
        );
    }

    /**
     * @param $table
     */
    public function truncateTable($table)
    {
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->getConnection();
        $connection->truncateTable($this->getTable($table));
    }
}
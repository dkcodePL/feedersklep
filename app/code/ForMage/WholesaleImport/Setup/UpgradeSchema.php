<?php

namespace ForMage\WholesaleImport\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->createTaskTable($setup);
        }

    }

    protected function createTaskTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('4mage_wholesaleimport_task'))
            ->addColumn(
                'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
            )->addColumn(
                'user_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false], 'User Id'
            )->addColumn(
                'status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Status'
            )->addColumn(
                'type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Type'
            )->addColumn(
                'filename', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Filename'
            )->addColumn(
                'wholesale_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Wholesale Id'
            )->addColumn(
                'products', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['nullable' => false], 'Products'
            )->addColumn(
                'messages', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Messages'
            )->addColumn(
                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Date'
            )->addColumn(
                'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Date'
            );


//        $installer->getConnection()->addIndex(
//            $installer->getTable('4mage_wholesaleimport_task'),
//            $installer->getConnection()->getIndexName(
//                $installer->getTable('4mage_wholesaleimport_task'),
//                'wholesale_id',
//                'index'
//            ),
//            'id'
//        );
//
//        $installer->getConnection()->addForeignKey(
//            $installer->getFkName(
//                '4mage_wholesaleimport_wholesale',
//                'id',
//                '4mage_wholesaleimport_task',
//                'wholesale_id'
//            ),
//            $installer->getTable('4mage_wholesaleimport_task'),
//            'wholesale_id',
//            $installer->getTable('4mage_wholesaleimport_wholesale'),
//            'id',
//            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
//        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();

        return $this;
    }


}

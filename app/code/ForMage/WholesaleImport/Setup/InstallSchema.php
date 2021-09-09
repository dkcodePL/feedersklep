<?php

namespace ForMage\WholesaleImport\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Install schema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('4mage_wholesaleimport_wholesale'))
            ->addColumn(
                'id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true], 'Id'
            )->addColumn(
                'type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Type'
            )->addColumn(
                'update_products', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Update'
            )->addColumn(
                'update_stocks', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Update'
            )->addColumn(
                'update_prices', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Update'
            )->addColumn(
                'is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Is Active'
            )->addColumn(
                'filename', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Filename'
            )->addColumn(
                'wholesale_code',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Wholesale Code'
            )->addColumn(
                'wholesale_name',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Wholesale Name'
            )->addColumn(
                'wholesale_attribute_id',  \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['nullable' => false, 'default' => '0', 'unsigned' => true], 'Wholesale Attribute Id'
            )->addColumn(
                'wholesale_model',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 254, ['nullable' => false], 'Wholesale Model'
            )->addColumn(
                'wholesale_data',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false,], 'Wholesale Data'
            )->addColumn(
                'attributes',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Attributes'
            )->addColumn(
                'custom_attributes',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Attributes'
            )->addColumn(
                'custom',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', ['nullable' => false], 'Custom'
            )->addColumn(
                'categories',  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', ['nullable' => false], 'Categories'
            )->addColumn(
                'stocks_updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true], 'Date'
            )->addColumn(
                'prices_updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true], 'Date'
            )->addColumn(
                'products_updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, ['nullable' => true], 'Date'
            )->addColumn(
                'updated_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Date'
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();

        return $this;
    }
}
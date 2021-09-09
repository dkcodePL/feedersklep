<?php

namespace Smartmage\Paczkomaty2Light\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $setup->getConnection()
            ->addColumn(
                $setup->getTable('quote'),
                'paczkomaty_target_point',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 32,
                    'default' => '',
                    'comment' => 'Wybrana maszyna'
                ]
            );

        $installer->endSetup();

    }
}

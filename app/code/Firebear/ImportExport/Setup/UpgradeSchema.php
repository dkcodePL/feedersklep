<?php
/**
 * Copyright © 2016 Firebear Studio. All rights reserved.
 */

namespace Firebear\ImportExport\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Module\Manager;
use Firebear\ImportExport\Setup\Operations\AddFieldHistoryIsMove;
use Firebear\ImportExport\Setup\Operations\CreateReplacingTable;

/**
 * Upgrade the extension
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    protected $eavSetup;

    protected $installSchema;

    protected $module;

    /**
     * @var AddFieldHistoryIsMove
     */
    protected $addFieldHistoryIsMove;

    /**
     * @var CreateReplacingTable
     */
    private $createReplacingTable;

    /**
     * UpgradeSchema constructor.
     * @param EavSetup $eavSetup
     * @param InstallSchema $installSchema
     * @param Manager $module
     * @param AddFieldHistoryIsMove $addFieldHistoryIsMove
     * @param CreateReplacingTable $createReplacingTable
     */
    public function __construct(
        EavSetup $eavSetup,
        InstallSchema $installSchema,
        Manager $module,
        AddFieldHistoryIsMove $addFieldHistoryIsMove,
        CreateReplacingTable $createReplacingTable
    ) {
        $this->eavSetup = $eavSetup;
        $this->installSchema = $installSchema;
        $this->module = $module;
        $this->addFieldHistoryIsMove = $addFieldHistoryIsMove;
        $this->createReplacingTable = $createReplacingTable;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            if ($this->module->isEnabled('Firebear_ImportExport')) {
                $this->installSchema->install($setup, $context);
            }
        }
        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addMappingTable($setup);
        }
        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->addForExport($setup);
            $this->changeNameTable($setup);
        }
        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->addColumn($setup);
        }
        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->addHistorys($setup);
        }

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->addHColumnsForExport($setup);
            $this->addTableImport($setup);
        }
        if (version_compare($context->getVersion(), '2.1.1', '<')) {
            $this->addColumnMapping($setup);
        }
        if (version_compare($context->getVersion(), '2.1.2', '<')) {
            $this->addPriceMapping($setup);
        }
        if (version_compare($context->getVersion(), '2.1.6', '<')) {
            $this->addFieldXslt($setup);
        }
        if (version_compare($context->getVersion(), '2.1.7', '<')) {
            $this->addFieldXsltForExport($setup);
        }
        if (version_compare($context->getVersion(), '2.1.8', '<')) {
            $this->addFieldToMapping($setup);
        }
        if (version_compare($context->getVersion(), '3.0.1', '<')) {
            $this->addExportJobEventTable($setup);
        }
        if (version_compare($context->getVersion(), '3.1.7', '<')) {
            $this->changeXsltField($setup);
        }
        if (version_compare($context->getVersion(), '3.1.8-alpha.1', '<')) {
            $this->addFieldTranslateFrom($setup);
            $this->addFieldTranslateTo($setup);
        }
        if (version_compare($context->getVersion(), '3.5.1-alpha.3', '<')) {
            $this->addFieldOrderToMaps($setup);
            $this->createReplacingTable->execute($setup);
        }
        if (version_compare($context->getVersion(), '3.5.1-alpha.4', '<')) {
            $this->addFieldHistoryIsMove->execute($setup);
        }
        if (version_compare($context->getVersion(), '3.6.1.1', '<')) {
            $this->addLogContentToHistory($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add export job event table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     */
    protected function addExportJobEventTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'firebear_export_jobs_event'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('firebear_export_jobs_event')
        )->addColumn(
            'job_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Job Id'
        )->addColumn(
            'event',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'primary' => true],
            'Event name'
        )->addIndex(
            $setup->getIdxName('firebear_export_jobs_event', ['event']),
            ['event']
        )->addForeignKey(
            $setup->getFkName('firebear_export_jobs_event', 'job_id', 'firebear_export_jobs', 'entity_id'),
            'job_id',
            $setup->getTable('firebear_export_jobs'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Export job event'
        );
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add mapping table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return $this
     */
    protected function addMappingTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'eav_attribute_option_value'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('import_job_mapping')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'job_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Job Id'
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true, 'default' => null],
            'Magento Attribute Id'
        )->addColumn(
            'special_attribute',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Special System Attribute'
        )->addColumn(
            'import_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Import Attribute Code'
        )->addIndex(
            $setup->getIdxName('import_job_mapping', ['job_id']),
            ['job_id']
        )->addIndex(
            $setup->getIdxName('import_job_mapping', ['attribute_id']),
            ['attribute_id']
        )->addIndex(
            $setup->getIdxName(
                'import_job_mapping',
                ['job_id', 'attribute_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['job_id', 'attribute_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName('import_job_mapping', 'job_id', 'import_jobs', 'entity_id'),
            'job_id',
            $setup->getTable('import_jobs'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('import_job_mapping', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Import Attributes Mapping'
        );
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    protected function addForExport(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('firebear_export_jobs')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Job Id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Job Active'
        )->addColumn(
            'cron',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Cron schedule'
        )->addColumn(
            'frequency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            10,
            ['nullable' => false],
            'Frequency'
        )->addColumn(
            'entity',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Entity Type'
        )->addColumn(
            'behavior_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Behavior Data (json)'
        )->addColumn(
            'export_source',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Export Source'
        )->addColumn(
            'source_data',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Source Data (json)'
        )->setComment(
            'Export Jobs'
        )->addColumn(
            'file_updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'File Updated At'
        )->setComment(
            'File Updated At'
        );
        $setup->getConnection()->createTable($table);
    }

    protected function changeNameTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->renameTable(
            $setup->getTable('import_jobs'),
            $setup->getTable('firebear_import_jobs')
        );
        $setup->getConnection()->renameTable(
            $setup->getTable('import_job_mapping'),
            $setup->getTable('firebear_import_job_mapping')
        );
    }

    public function addColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_job_mapping'),
            'default_value',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Default Value'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addHistorys(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('firebear_import_history')
        )->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'job_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Job Id'
        )->addColumn(
            'started_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Started'
        )->addColumn(
            'finished_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Finished'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Type'
        )->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Imported File'
        )->setComment(
            'Export Jobs'
        )->addIndex(
            $setup->getIdxName('firebear_import_history', ['history_id']),
            ['history_id']
        )->addForeignKey(
            $setup->getFkName('firebear_import_history', 'job_id', 'firebear_import_jobs', 'entity_id'),
            'job_id',
            $setup->getTable('firebear_import_jobs'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($table);
        $table = $setup->getConnection()->newTable(
            $setup->getTable('firebear_export_history')
        )->addColumn(
            'history_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'History Id'
        )->addColumn(
            'job_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Job Id'
        )->addColumn(
            'started_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Started'
        )->addColumn(
            'finished_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Finished'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Type'
        )->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Exported File'
        )->setComment(
            'Export Jobs'
        )->addIndex(
            $setup->getIdxName('firebear_export_history', ['history_id']),
            ['history_id']
        )->addForeignKey(
            $setup->getFkName('firebear_export_history', 'job_id', 'firebear_export_jobs', 'entity_id'),
            'job_id',
            $setup->getTable('firebear_export_jobs'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addHColumnsForExport(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_export_history'),
            'temp_file',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'size' => 255,
                'nullable' => true,
                'comment' => 'Temp file export'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addTableImport(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable($setup->getTable('firebear_importexport_importdata'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'entity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Entity'
            )
            ->addColumn(
                'behavior',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['nullable' => false, 'default' => 'append'],
                'Behavior'
            )
            ->addColumn(
                'subentity',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'SubEntity'
            )
            ->addColumn(
                'file',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'File'
            )
            ->addColumn(
                'job_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Job Id'
            )
            ->addColumn(
                'data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '4G',
                ['default' => false],
                'Data'
            )
            ->setComment('Firebear Import Data Table')
            ->addForeignKey(
                $setup->getFkName('firebear_importexport_importdata', 'job_id', 'firebear_import_jobs', 'entity_id'),
                'job_id',
                $setup->getTable('firebear_import_jobs'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table);
    }

    public function addColumnMapping(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_jobs'),
            'mapping',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'mapping field'
            ]
        );
    }

    /**
     * Add new column `price_rules` to `firebear_import_jobs` table
     *
     * @param SchemaSetupInterface $setup
     */
    public function addPriceMapping(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_jobs'),
            'price_rules',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'Price rules'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addFieldXslt(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_jobs'),
            'xslt',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'Xslt'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addFieldXsltforExport(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_export_jobs'),
            'xslt',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'Xslt'
            ]
        );
    }

    public function addFieldToMapping(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_job_mapping'),
            'custom',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Default Value'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addFieldTranslateFrom(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_jobs'),
            'translate_from',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Translate from'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addFieldTranslateTo(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_jobs'),
            'translate_to',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'comment' => 'Translate to'
            ]
        );
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function changeXsltField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('firebear_import_jobs'),
            'xslt',
            'xslt',
            [
                'type' => 'longblob',
                'nullable' => true,
                'comment' => 'Xslt'
            ]
        );
        $setup->getConnection()->changeColumn(
            $setup->getTable('firebear_export_jobs'),
            'xslt',
            'xslt',
            [
                'type' => 'longblob',
                'nullable' => true,
                'comment' => 'Xslt'
            ]
        );
    }

    public function addFieldOrderToMaps(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $setup->getConnection()->addColumn(
            $setup->getTable('firebear_import_job_mapping'),
            'position',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'comment' => 'Position'
            ]
        );
        $setup->endSetup();
    }

    public function addLogContentToHistory(SchemaSetupInterface $setup)
    {
        $setup->startSetup();

        $tableFirebearImportHistory = $setup->getTable('firebear_import_history');
        $tableFirebearExportHistory = $setup->getTable('firebear_export_history');

        // import history
        $setup->getConnection()->addColumn(
            $tableFirebearImportHistory,
            'db_log_storage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'comment' => 'db_log_storage'
            ]
        );

        $setup->getConnection()->addColumn(
            $tableFirebearImportHistory,
            'log_content',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'log_content'
            ]
        );

        // export history
        $setup->getConnection()->addColumn(
            $tableFirebearExportHistory,
            'db_log_storage',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => false,
                'comment' => 'db_log_storage'
            ]
        );

        $setup->getConnection()->addColumn(
            $tableFirebearExportHistory,
            'log_content',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BLOB,
                'nullable' => true,
                'comment' => 'log_content'
            ]
        );

        $setup->endSetup();
    }
}

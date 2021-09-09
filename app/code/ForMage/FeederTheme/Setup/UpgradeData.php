<?php

namespace ForMage\FeederTheme\Setup;

use Magento\Framework\Setup\{
    ModuleContextInterface,
    ModuleDataSetupInterface,
    UpgradeDataInterface
};
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    /**
     * Sales setup factory
     *
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        SalesSetupFactory $salesSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->manufacturerCategoryField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addPaczkomatField($setup);
        }

        $setup->endSetup();
    }

    public function manufacturerCategoryField(ModuleDataSetupInterface $setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'manufacturer', [
            'type' => 'int',
            'label' => 'Manufacturer',
            'input' => 'select',
            'source' => \ForMage\FeederTheme\Model\Category\Attribute\Source\Manufacturer::class,
            'visible' => true,
            'used_in_product_listing' => false,
            'required' => false,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Brand',
        ]);
    }

    public function addPaczkomatField(ModuleDataSetupInterface $setup) {

        /** @var \Magento\Sales\Setup\SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $salesSetup->addAttribute('order', 'paczkomat', ['type' => 'varchar', 'length' => 254, 'required' => false, 'grid' => true,  'comment' => 'Paczkomat']);
    }


}
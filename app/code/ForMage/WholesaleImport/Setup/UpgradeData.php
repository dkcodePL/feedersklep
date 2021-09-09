<?php

namespace ForMage\WholesaleImport\Setup;

use Magento\Framework\Setup\{
    ModuleContextInterface,
    ModuleDataSetupInterface,
    UpgradeDataInterface
};
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category;
use ForMage\WholesaleImport\Helper\Data as Helper;


class UpgradeData implements UpgradeDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addWholesaleField($setup);
        }
        $setup->endSetup();
    }



    public function addWholesaleField(ModuleDataSetupInterface $setup)
    {

        $fields = [
            'wholesale' => [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Wholesale',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Table::class,
                'class' => '',
                'option' => ['value' =>
                    [
                        'option_1' => [
                            0 => 'Wholesale 1',
                        ],
                        'option_2' => [
                            0 => 'Wholesale 2',
                        ],
                        'option_3' => [
                            0 => 'Wholesale 3',
                        ],

                    ],
                    'order' =>
                        [
                            'option_1' => 1,
                            'option_2' => 2,
                            'option_3' => 3,
                        ]
                ],
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'is_used_for_promo_rules' => true,
                'used_in_product_listing' => false,
                'wysiwyg_enabled' => false,
                'unique' => false,
                'apply_to' => ''
            ],


        ];

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        foreach ($fields as $code => $field) {

            if ($eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, $code)) {
                continue;
            }
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $code, $field);
        }

    }


}
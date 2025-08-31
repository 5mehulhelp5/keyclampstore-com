<?php
namespace Best4Mage\DPPC\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     * @return void
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->_eavSetupFactory = $eavSetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            /** @var EavSetup $eavSetup */
            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_product_enable');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_product_enable',
                [
                    'type'                      => 'int',
                    'backend'                   => '',
                    'frontend'                  => '',
                    'label'                     => 'Enable DPPC',
                    'input'                     => 'boolean',
                    'class'                     => '',
                    'source'                    => '',
                    'global'                    => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'                   => true,
                    'required'                  => false,
                    'user_defined'              => true,
                    'default'                   => 0,
                    'searchable'                => false,
                    'filterable'                => false,
                    'comparable'                => false,
                    'visible_on_front'          => false,
                    'used_in_product_listing'   => true,
                    'apply_to'                  => 'simple,configurable',
                    'group'                     => 'Best4Mage DPPC Settings',
                    'sort_order'        => 0,
                ]
            );

            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_product_shapes');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_product_shapes',
                [
                    'type'              => 'varchar',
                    'backend'           => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'unit'              => '',
                    'frontend'          => '',
                    'label'             => 'Choose your Shape',
                    'input'             => 'multiselect',
                    'class'             => '',
                    'source'            => 'Best4Mage\DPPC\Model\Config\Source\Shape',
                    'global'            => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => true,
                    'default'           => '',
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'apply_to'          => 'simple,configurable',
                    'group'             => 'Best4Mage DPPC Settings',
                    'sort_order'        => 1,
                ]
            );

            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_unit_price');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_unit_price',
                [
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Unit Price',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => true,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable',
                    'group'    => 'Best4Mage DPPC Settings',
                    'sort_order'        => 2,
                ]
            );
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_min_unit');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_min_unit',
                [
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Minimum Unit',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable',
                    'group'    => 'Best4Mage DPPC Settings',
                    'sort_order'        => 3,
                ]
            );
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_max_unit');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_max_unit',
                [
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Maximum Unit',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable',
                    'group'    => 'Best4Mage DPPC Settings',
                    'sort_order'        => 4,
                ]
            );
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_input_unit');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_input_unit',
                [
                    'type'              => 'varchar',
                    'backend'           => '',
                    'frontend'          => '',
                    'label'             => 'Input Unit',
                    'input'             => 'select',
                    'class'             => '',
                    'source'            => 'Best4Mage\DPPC\Model\Config\Source\Unit',
                    'global'            => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => false,
                    'default'           => '',
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'apply_to'          => 'simple,configurable',
                    'group'             => 'Best4Mage DPPC Settings',
                    'sort_order'        => 5,
                ]
            );
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_output_unit');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_output_unit',
                [
                    'type'              => 'varchar',
                    'backend'           => '',
                    'frontend'          => '',
                    'label'             => 'Output Unit',
                    'input'             => 'select',
                    'class'             => '',
                    'source'            => 'Best4Mage\DPPC\Model\Config\Source\Unit',
                    'global'            => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => true,
                    'default'           => 0,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'apply_to'          => 'simple,configurable',
                    'group'             => 'Best4Mage DPPC Settings',
                    'sort_order'        => 6,
                ]
            );
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'dppc_minimum_area_price');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_minimum_area_price',
                [
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Minimum Price',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple,configurable',
                    'group'    => 'Best4Mage DPPC Settings',
                    'sort_order'        => 7,
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.6') < 0) {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_min_unit',
                'label',
                'Minimum Output Unit'
            );

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_max_unit',
                'label',
                'Maximum Output Unit'
            );

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_min_unit',
                'note',
                'Enter Minimum value allowed as measurement (area/volume) of this product based on your "Output Unit".'
            );

            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'dppc_max_unit',
                'note',
                'Enter Maximum value allowed as measurement (area/volume) of this product based on your "Output Unit".'
            );
        }
    }
}

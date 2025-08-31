<?php

namespace Best4Mage\DPPC\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $dppcSideTable = $installer->getConnection()
            ->newTable($installer->getTable('dppc_side'))
            ->addColumn(
                'side_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Side ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false], 'Side Title')
            ->addColumn('code', Table::TYPE_TEXT, '255', ['nullable' => false], 'Code')
            ->setComment('DPPC Side Table');

        $installer->getConnection()->createTable($dppcSideTable);

        $dppcShapeTable = $installer->getConnection()
            ->newTable($installer->getTable('dppc_shape'))
            ->addColumn(
                'shape_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Shape ID'
            )
            ->addColumn('title', Table::TYPE_TEXT, 255, ['nullable' => false], 'Shape Title')
            ->addColumn('side_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'Side Ids')
            ->addColumn('image', Table::TYPE_TEXT, '255', ['nullable' => true], 'Shape Image')
            ->addColumn('min_max_value', Table::TYPE_TEXT, null, ['nullable' => true], 'Sides Min-Max Value')
            ->addColumn('sort_order', Table::TYPE_SMALLINT, null, ['nullable' => true], 'Sort Order')
            ->addColumn('calculation_type', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Calculation Type')
            ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Status')
            ->addColumn('created_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Created At')
            ->addColumn('updated_at', Table::TYPE_DATETIME, null, ['nullable' => false], 'Updated At')
            ->setComment('DPPC Shape Table');

        $installer->getConnection()->createTable($dppcShapeTable);

        $installer->endSetup();
    }
}

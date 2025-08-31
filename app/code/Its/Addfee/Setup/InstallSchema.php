<?php

namespace Its\Addfee\Setup;

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
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'powdercoating_fees'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('powdercoating_fees')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'powdercoating_fees'
        )
		->addColumn(
            'orderno',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'orderno'
        )
		->addColumn(
            'fee',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'fee'
        ) ;
		/*{{CedAddTableColumn}}}*/
		  
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}

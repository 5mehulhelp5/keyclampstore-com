<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SecurityPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SecurityPro\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Mageplaza\SecurityPro\Setup
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

        if (!$installer->tableExists('mageplaza_security_action_log')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_security_action_log'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Action Log')
                ->addColumn('time', Table::TYPE_TIMESTAMP, null, [], 'Log Time')
                ->addColumn('user_name', Table::TYPE_TEXT, '64k', [], 'User Name')
                ->addColumn('ip', Table::TYPE_TEXT, '64k', [], 'IP address')
                ->addColumn('action', Table::TYPE_TEXT, '64k', [], 'Action')
                ->addColumn('module', Table::TYPE_TEXT, '64k', [], 'Module')
                ->addColumn('status', Table::TYPE_BOOLEAN, null, [], 'Status')
                ->addColumn('full_action_name', Table::TYPE_TEXT, '64k', [], 'Full Action Name')
                ->addColumn('description', Table::TYPE_TEXT, '64k', [], 'Description')
                ->setComment('Mageplaza Security Action Log Table');

            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('mageplaza_security_file_change')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_security_file_change'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Action Log Backup')
                ->addColumn('file_name', Table::TYPE_TEXT, '64k', [], 'File Name')
                ->addColumn('path', Table::TYPE_TEXT, '64k', [], 'File Path')
                ->addColumn('old_hash', Table::TYPE_TEXT, '64k', [], 'Old Hash')
                ->addColumn('new_hash', Table::TYPE_TEXT, '64k', [], 'New Hash')
                ->addColumn('type', Table::TYPE_TEXT, '64k', [], 'New Hash')
                ->addColumn('time', Table::TYPE_TIMESTAMP, null, [], 'Time')
                ->setComment('Mageplaza Security File Change Table');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}

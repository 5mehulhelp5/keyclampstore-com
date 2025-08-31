<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SecurityPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SecurityPro\Controller\Adminhtml\Checklist;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentWriter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Setup\Validator\DbValidator;
use Magento\Store\Model\Store;
use Zend_Db_Statement_Exception;

/**
 * Class Fixit
 * @package Mageplaza\SecurityPro\Controller\Adminhtml\Checklist
 */
class Fixit extends Action
{
    /**
     * @var Writer
     */
    protected $_storageWriter;

    /**
     * @var DeploymentWriter
     */
    protected $_deploymentConfigWriter;

    /**
     * @var ResourceConnection
     */
    protected $_connection;

    /**
     * @var TypeListInterface
     */
    protected $_cache;

    /**
     * @var DbValidator
     */
    protected $_dbValidator;

    /**
     * Fixit constructor.
     *
     * @param Context $context
     * @param Writer $storageWriter
     * @param DeploymentWriter $deploymentConfigWriter
     * @param ResourceConnection $connection
     * @param TypeListInterface $typeList
     * @param DbValidator $dbValidator
     */
    public function __construct(
        Context $context,
        Writer $storageWriter,
        DeploymentWriter $deploymentConfigWriter,
        ResourceConnection $connection,
        TypeListInterface $typeList,
        DbValidator $dbValidator
    ) {
        parent::__construct($context);

        $this->_storageWriter          = $storageWriter;
        $this->_deploymentConfigWriter = $deploymentConfigWriter;
        $this->_connection             = $connection;
        $this->_cache                  = $typeList;
        $this->_dbValidator            = $dbValidator;
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws Zend_Db_Statement_Exception
     */
    public function execute()
    {
        $action = $this->_request->getParam('id');
        switch ($action) {
            case 'frontend_captcha':
                $this->_storageWriter->save(
                    'customer/captcha/enable',
                    1,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );
                break;
            case 'backend_captcha':
                $this->_storageWriter->save(
                    'admin/captcha/enable',
                    1,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );
                break;
            case 'db_prefix':
                $newPrefix = $this->_request->getParam('prefix');
                if ($newPrefix && $this->_dbValidator->checkDatabaseTablePrefix($newPrefix)) {
                    try {
                        $this->_deploymentConfigWriter->saveConfig([
                            'app_env' => [
                                'db' => [
                                    'table_prefix' => $newPrefix
                                ]
                            ]
                        ]);
                        $err = 0;
                    } catch (Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                        $err = 1;
                    }
                    if (!$err) {
                        $tables = $this->_connection->getConnection()->query('SHOW TABLES')->fetchAll();
                        foreach ($tables as $table) {
                            $tableName    = reset($table);
                            $newTableName = $newPrefix . $tableName;
                            $this->_connection->getConnection()->query("RENAME TABLE `$tableName`  TO `$newTableName`");
                        }
                    }
                }
                break;
        }
        $this->_cache->cleanType('config');
        $this->_cache->cleanType('full_page');

        return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
    }
}

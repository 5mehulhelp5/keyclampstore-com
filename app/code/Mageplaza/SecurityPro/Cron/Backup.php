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

namespace Mageplaza\SecurityPro\Cron;

use Exception;
use Magento\Cron\Model\Config\Source\Frequency;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\Store;
use Mageplaza\SecurityPro\Helper\Data;
use Mageplaza\SecurityPro\Model\ResourceModel\ActionLog\CollectionFactory;
use Mageplaza\SecurityPro\Model\ResourceModel\ActionLogFactory;

/**
 * Class Backup
 * @package Mageplaza\SecurityPro\Cron
 */
class Backup
{
    const BACKUP_FILE_PATH = BP . '/var/backup/mageplaza_security_action_log';

    /**
     * @var Csv
     */
    protected $_csv;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var File
     */
    protected $_file;

    /**
     * @var ActionLogFactory
     */
    protected $_actionLogResource;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Writer
     */
    protected $_writer;

    /**
     * @var TypeListInterface
     */
    protected $_cache;

    /**
     * Backup constructor.
     *
     * @param Writer $writer
     * @param Data $helper
     * @param File $file
     * @param Csv $csv
     * @param CollectionFactory $collectionFactory
     * @param ActionLogFactory $actionLogResource
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Writer $writer,
        Data $helper,
        File $file,
        Csv $csv,
        CollectionFactory $collectionFactory,
        ActionLogFactory $actionLogResource,
        TypeListInterface $cacheTypeList
    ) {
        $this->_cache             = $cacheTypeList;
        $this->_writer            = $writer;
        $this->_helper            = $helper;
        $this->_actionLogResource = $actionLogResource;
        $this->_file              = $file;
        $this->_collectionFactory = $collectionFactory;
        $this->_csv               = $csv;
    }

    /**
     * @throws Exception
     */
    public function execute()
    {
        $this->_cache->cleanType('full_page');
        $this->_cache->cleanType('config');

        if ($this->_helper->isEnabled() && $this->isBackup() && $this->_helper->getConfigBackUp('enabled')) {
            $head       = [
                'id',
                'time',
                'user_name',
                'ip',
                'action',
                'module',
                'status',
                'full_action_name',
                'description'
            ];
            $collection = $this->_collectionFactory->create();
            $data       = $collection->getData();
            if (count($data)) {
                foreach ($data as &$item) {
                    $item['time'] = $this->_helper->convertToLocaleTime($item['time'], 'Y-m-d H:i:s');
                }
                array_unshift($data, $head);
                $this->_file->checkAndCreateFolder(self::BACKUP_FILE_PATH);
                $now      = time();
                $fileName = date('mdYHis', $now) . '.csv';
                $fileUrl  = self::BACKUP_FILE_PATH . '/' . $fileName;
                $this->_csv->saveData($fileUrl, $data);
                $this->_writer->save(
                    'security/action_log_backup/last_backup',
                    date('Y-m-d H:i:s'),
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );
                if ($this->_helper->getConfigBackUp('clear_log')) {
                    $actionLogResource = $this->_actionLogResource->create();
                    $actionLogResource->getConnection()->delete($actionLogResource->getMainTable());
                }
            }
        }
    }

    /**
     * Check Backup Frequency
     *
     * @return bool
     */
    private function isBackup()
    {
        $lastBackup = $this->_helper->getConfigBackUp('last_backup');
        $frequency  = $this->_helper->getConfigBackUp('frequency');
        $day        = (int) date('z', time() - strtotime($lastBackup));

        switch ($frequency) {
            case Frequency::CRON_DAILY:
                return true;
            case Frequency::CRON_WEEKLY:
                return (date('w') === '1' && $day > 5);
            case Frequency::CRON_MONTHLY:
                return (date('j') === '1' && $day > 10);
        }

        return false;
    }
}

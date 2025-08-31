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
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\SecurityPro\Helper\Data;
use Mageplaza\SecurityPro\Model\ResourceModel\FileChange as FileChangeResource;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class FileChange
 * @package Mageplaza\SecurityPro\Cron
 */
class FileChange
{
    /**
     * @var File
     */
    protected $_file;

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var FileChangeResource
     */
    protected $_fileChangeResource;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * FileChange constructor.
     *
     * @param Data $helper
     * @param File $file
     * @param FileChangeResource $fileChangeResource
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param UrlInterface $backendUrl
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Data $helper,
        File $file,
        FileChangeResource $fileChangeResource,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        UrlInterface $backendUrl,
        ManagerInterface $messageManager
    ) {
        $this->_helper             = $helper;
        $this->_file               = $file;
        $this->_fileChangeResource = $fileChangeResource;
        $this->_transportBuilder   = $transportBuilder;
        $this->_storeManager       = $storeManager;
        $this->_logger             = $logger;
        $this->_backendUrl         = $backendUrl;
        $this->messageManager      = $messageManager;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws Exception
     */
    public function execute()
    {
        if ($this->_helper->isEnabled() && $this->_helper->getConfigFileChange('enabled')) {
            $this->processFileChange();
        }
    }

    /**
     * @param bool $isSendAlert
     * @param bool $isSaveMaster
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function processFileChange($isSendAlert = true, $isSaveMaster = false)
    {
        $excludeFolders = $this->_helper->getConfigFileChange('exclude_folder');
        if ($excludeFolders) {
            $excludeFolders = explode(PHP_EOL, $excludeFolders);
            $excludeFolders = array_map('trim', $excludeFolders);
        } else {
            $excludeFolders = [];
        }
        $excludeFileTypes = $this->_helper->getConfigFileChange('exclude_file');
        if ($excludeFileTypes) {
            $excludeFileTypes = explode(',', $excludeFileTypes);
            $excludeFileTypes = array_map('trim', $excludeFileTypes);
        } else {
            $excludeFileTypes = [];
        }
        $config           = [
            'exclude_folder' => $excludeFolders,
            'exclude_file'   => $excludeFileTypes,
        ];
        $excludeFolders   = $config['exclude_folder'];
        $excludeFileTypes = $config['exclude_file'];
        $currentHashes    = $this->getCurrentHashes($excludeFolders, $excludeFileTypes);
        if ($isSaveMaster || !file_exists($this->getHashFile())) {
            $this->saveMasterHashes($currentHashes);

            return;
        }
        $masterHashes  = $this->loadMasterHashes();
        $newFiles      = array_diff_key($currentHashes, $masterHashes);
        $deletedFiles  = array_diff_key($masterHashes, $currentHashes);
        $changedFiles  = [];
        $intersectKeys = array_keys(array_intersect_key($masterHashes, $currentHashes));
        foreach ($intersectKeys as $intersectKey) {
            if ($masterHashes[$intersectKey] !== $currentHashes[$intersectKey]) {
                $changedFiles[$intersectKey] = $masterHashes[$intersectKey];
            }
        }
        if (count($newFiles) > 0 || count($deletedFiles) > 0 || count($changedFiles) > 0) {
            $data = $this->getFileChangeData($newFiles, $deletedFiles, $changedFiles, $currentHashes);
            $this->saveFileChange($data);

            if ($isSendAlert) {
                $this->sendAlertMail($data);
            }
            $this->saveMasterHashes($currentHashes);
        }
    }

    /**
     * @return array
     */
    protected function loadMasterHashes()
    {
        $masterFilePath = $this->getHashFile();
        if (!file_exists($masterFilePath)) {
            return [];
        }
        $hashes          = [];
        $masterFileLines = file($masterFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($masterFileLines as $masterFileLine) {
            $masterFileLine = explode('\n', $masterFileLine);
            foreach ($masterFileLine as $masterFile) {
                $masterFile = explode('=', $masterFile);
                if (count($masterFile) >= 2) {
                    $hashes[$masterFile[0]] = $masterFile[1];
                }
            }
        }

        return $hashes;
    }

    /**
     * @param $excludeFolders
     * @param $excludeFileTypes
     *
     * @return array
     */
    protected function getCurrentHashes($excludeFolders, $excludeFileTypes)
    {
        try {
            $currentHashes = [];
            $iterator      = new RecursiveDirectoryIterator(BP);
            foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
                if (!$file->isDir() && !$this->checkFolderExclude($file->getPathName(), $excludeFolders)) {
                    $fileType = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
                    if (!in_array(strtolower($fileType), $excludeFileTypes, true)) {
                        $hash                                = sha1_file($file->getPathname());
                        $currentHashes[$file->getPathname()] = $hash;
                    }
                }
            }

            return $currentHashes;
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }

    /**
     * @param $fullFileName
     * @param $excludeFolders
     *
     * @return bool
     */
    protected function checkFolderExclude($fullFileName, $excludeFolders)
    {
        $fileWithoutRootPath = str_replace(BP, '', $fullFileName);
        foreach ($excludeFolders as $folder) {
            if ($folder) {
                if (substr($folder, -1) !== '/') {
                    $folder .= '/';
                }
                if (strncmp($folder, '/', 1) === 0) {
                    if (strpos($fileWithoutRootPath, $folder) === 0) {
                        return true;
                    }
                } else {
                    $folder = '/' . $folder;
                    if (strpos($fileWithoutRootPath, $folder) !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $newFiles
     * @param $deletedFiles
     * @param $changedFiles
     * @param $currentHashes
     *
     * @return array
     */
    protected function getFileChangeData($newFiles, $deletedFiles, $changedFiles, $currentHashes)
    {
        $data = [];
        if (!empty($newFiles)) {
            foreach ($newFiles as $filePath => $hash) {
                $fileName            = explode('/', $filePath);
                $fileName            = end($fileName);
                $data['new_files'][] = [
                    'file_name' => $fileName,
                    'path'      => $filePath,
                    'old_hash'  => null,
                    'new_hash'  => $hash,
                    'type'      => 'created',
                    'time'      => $this->_helper->convertToLocaleTime(date('Y-m-d H:i:s', filectime($filePath)))
                ];
            }
        }
        if (!empty($deletedFiles)) {
            foreach ($deletedFiles as $filePath => $hash) {
                $fileName                = explode('/', $filePath);
                $fileName                = end($fileName);
                $data['deleted_files'][] = [
                    'file_name' => $fileName,
                    'path'      => $filePath,
                    'old_hash'  => $hash,
                    'new_hash'  => null,
                    'type'      => 'deleted',
                    'time'      => $this->_helper->convertToLocaleTime(date('Y-m-d H:i:s'))
                ];
            }
        }
        if (!empty($changedFiles)) {
            foreach ($changedFiles as $filePath => $hash) {
                $fileName                = explode('/', $filePath);
                $fileName                = end($fileName);
                $data['changed_files'][] = [
                    'file_name' => $fileName,
                    'path'      => $filePath,
                    'new_hash'  => $currentHashes[$filePath],
                    'old_hash'  => $hash,
                    'type'      => 'modified',
                    'time'      => $this->_helper->convertToLocaleTime(date('Y-m-d H:i:s', filemtime($filePath)))
                ];
            }
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @throws LocalizedException
     */
    protected function saveFileChange($data)
    {
        if (!empty($data)) {
            $arr = [];
            foreach ($data as $items) {
                foreach ($items as $item) {
                    $arr[] = $item;
                }
            }
            $this->_fileChangeResource->getConnection()
                ->insertMultiple($this->_fileChangeResource->getMainTable(), $arr);
        }
    }

    /**
     * @param $data
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function sendAlertMail($data)
    {
        if (!empty($data) && $this->_helper->getConfigGeneral('email')) {
            try {
                $sendTo       = explode(',', $this->_helper->getConfigGeneral('email'));
                $sendTo       = array_map('trim', $sendTo);
                $storeUrl     = parse_url($this->_backendUrl->getBaseUrl(), PHP_URL_HOST);
                $store        = $this->_storeManager->getStore();
                $templateVars = [
                    'logs'       => $data,
                    'viewLogUrl' => $this->_backendUrl->getUrl('mpsecurity/filechange/'),
                    'logo_url'   => 'https://www.mageplaza.com/media/mageplaza-security-email.png',
                    'logo_alt'   => 'Mageplaza',
                    'store_url'  => $storeUrl
                ];

                $this->_transportBuilder
                    ->setTemplateIdentifier($this->_helper->getConfigFileChange('email_template'))
                    ->setTemplateOptions([
                        'area'  => Area::AREA_FRONTEND,
                        'store' => $store->getId()
                    ])
                    ->setTemplateVars($templateVars)
                    ->setFrom('general');
                foreach ($sendTo as $to) {
                    $this->_transportBuilder->addTo($to);
                }
                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
            } catch (MailException $e) {
                $this->_logger->critical($e->getLogMessage());
            }
        }
    }

    /**
     * @param array $files
     *
     * @throws Exception
     */
    protected function saveMasterHashes(array $files)
    {
        $fileContent = '';
        foreach ($files as $key => $filepath) {
            $fileContent .= $key . '=' . $filepath . '\n';
        }
        $this->_file->checkAndCreateFolder($this->getHashFile(''));
        $fh = fopen($this->getHashFile(), 'w');
        fwrite($fh, $fileContent);
        fclose($fh);
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getHashFile($filename = 'masterHashes.txt')
    {
        return BP . '/var/backup' . ($filename ? '/' . $filename : '');
    }
}

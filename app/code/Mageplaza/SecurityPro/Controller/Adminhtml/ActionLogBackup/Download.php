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

namespace Mageplaza\SecurityPro\Controller\Adminhtml\ActionLogBackup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\ReadFactory;

/**
 * Class Download
 * @package Mageplaza\SecurityPro\Controller\Adminhtml\ActionLogBackup
 */
class Download extends Action
{
    const SAMPLE_FILES_MODULE = 'Magento_ImportExport';

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Download constructor.
     *
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param RawFactory $resultRawFactory
     * @param ReadFactory $readFactory
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        RawFactory $resultRawFactory,
        ReadFactory $readFactory
    ) {
        parent::__construct($context);

        $this->fileFactory      = $fileFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->readFactory      = $readFactory;
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface
     * @throws FileSystemException
     */
    public function execute()
    {
        $fileName      = $this->getRequest()->getParam('filename');
        $directoryRead = $this->readFactory->create(BP . '/var/backup/mageplaza_security_action_log');
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($directoryRead->readFile($fileName));

        return $resultRaw;
    }
}

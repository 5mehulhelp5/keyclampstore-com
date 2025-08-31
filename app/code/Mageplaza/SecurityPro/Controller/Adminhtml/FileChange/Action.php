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

namespace Mageplaza\SecurityPro\Controller\Adminhtml\FileChange;

use Exception;
use Magento\Backend\App\Action as AbstractAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\SecurityPro\Cron\FileChange;
use Mageplaza\SecurityPro\Helper\Data;
use Mageplaza\SecurityPro\Model\ResourceModel\FileChange as FileChangeResource;

/**
 * Class Action
 * @package Mageplaza\SecurityPro\Controller\Adminhtml\FileChange
 */
class Action extends AbstractAction
{
    /**
     * @var bool|PageFactory
     */
    protected $resultPageFactory = false;

    /**
     * @var FileChangeResource
     */
    protected $_fileChangeResource;

    /**
     * @var FileChange
     */
    protected $_fileChange;

    /**
     * @var Data
     */
    protected $_securityHelper;

    /**
     * Action constructor.
     *
     * @param Data $securityHelper
     * @param FileChangeResource $fileChangeResource
     * @param FileChange $fileChange
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Data $securityHelper,
        FileChangeResource $fileChangeResource,
        FileChange $fileChange,
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory   = $resultPageFactory;
        $this->_fileChangeResource = $fileChangeResource;
        $this->_fileChange         = $fileChange;
        $this->_securityHelper     = $securityHelper;
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws Exception
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->_securityHelper->isEnabled()) {
            $id = $this->_request->getParam('id');
            switch ($id) {
                case 'clear':
                    $this->_fileChangeResource->getConnection()->delete($this->_fileChangeResource->getMainTable());
                    break;
                case 'check':
                    $this->_fileChange->processFileChange(false);
                    break;
                case 'create_master':
                    try {
                        $this->_fileChange->processFileChange(false, true);
                        $this->messageManager->addSuccessMessage(__('Reindex Successfully.'));
                    } catch (Exception $e) {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                    break;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Module is disabled'));
        }

        return $this->resultRedirectFactory->create()->setPath($this->_redirect->getRefererUrl());
    }
}

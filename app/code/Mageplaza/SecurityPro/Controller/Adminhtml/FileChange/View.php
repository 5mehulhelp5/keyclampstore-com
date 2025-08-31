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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\SecurityPro\Model\FileChangeFactory;

/**
 * Class View
 * @package Mageplaza\SecurityPro\Controller\Adminhtml\FileChange
 */
class View extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FileChangeFactory
     */
    protected $_logFactory;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FileChangeFactory $logFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FileChangeFactory $logFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry          = $registry;
        $this->_logFactory       = $logFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|Redirect
     * |\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $log = $this->initLog();
        if (!$log) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('mpsecurity/filechange/');

            return $resultRedirect;
        }
        $this->registry->register('mageplaza_security_filechange', $log);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('File Change record details'));
        $resultPage->getConfig()->getTitle()->prepend(__('File Change record details'));

        return $resultPage;
    }

    /**
     * @param bool $register
     *
     * @return $this|bool|null
     */
    protected function initLog($register = false)
    {
        $logId = (int) $this->getRequest()->getParam('id');
        $log   = $this->_logFactory->create();

        if ($logId) {
            $log = $log->load($logId);
            if (!$log->getId()) {
                $this->messageManager->addErrorMessage(__('This log no longer exists.'));

                return false;
            }
        }

        if ($register) {
            $this->registry->register('mageplaza_security_filechange', $log);
        }

        return $log;
    }
}

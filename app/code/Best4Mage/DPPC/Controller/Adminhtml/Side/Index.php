<?php
namespace Best4Mage\DPPC\Controller\Adminhtml\Side;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Best4Mage_DPPC::side');
        $resultPage->addBreadcrumb(__('DPPC Sides'), __('DPPC Sides'));
        $resultPage->addBreadcrumb(__('Manage DPPC Sides'), __('Manage DPPC Sides'));
        $resultPage->getConfig()->getTitle()->prepend(__('DPPC Sides'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the dppc side grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Best4Mage_DPPC::side');
    }
}

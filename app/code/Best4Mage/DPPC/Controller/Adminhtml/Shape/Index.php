<?php
namespace Best4Mage\DPPC\Controller\Adminhtml\Shape;

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
        $resultPage->setActiveMenu('Best4Mage_DPPC::shape');
        $resultPage->addBreadcrumb(__('DPPC Shapes'), __('DPPC Shapes'));
        $resultPage->addBreadcrumb(__('Manage DPPC Shapes'), __('Manage DPPC Shapes'));
        $resultPage->getConfig()->getTitle()->prepend(__('DPPC Shapes'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the dppc shape grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Best4Mage_DPPC::shape');
    }
}

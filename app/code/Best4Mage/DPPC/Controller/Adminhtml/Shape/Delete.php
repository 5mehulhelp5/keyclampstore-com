<?php
namespace Best4Mage\DPPC\Controller\Adminhtml\Shape;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Best4Mage_DPPC::delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('shape_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_objectManager->create('Best4Mage\DPPC\Model\Shape');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The shape has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['shape_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a shape to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}

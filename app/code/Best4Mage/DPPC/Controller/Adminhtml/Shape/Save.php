<?php
namespace Best4Mage\DPPC\Controller\Adminhtml\Shape;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Image\AdapterFactory;
     */
    protected $_adapterFactory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory;
     */
    protected $_uploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem;
     */
    protected $_filesystem;

    /**
     * Validate callbacks storage
     *
     * @var array
     * @access protected
     */
    protected $_validateCallbacks = [];

    /**
     * Uploaded file handle (copy of $_FILES[] element)
     *
     * @var array
     * @access protected
     */
    protected $_file;


    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_adapterFactory = $adapterFactory;
        $this->_uploaderFactory = $uploaderFactory;
        $this->_filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Best4Mage_DPPC::save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data  = $this->getRequest()->getPostValue();
        $files = $this->getRequest()->getFiles();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Best4Mage\DPPC\Model\Shape $model */
            $model = $this->_objectManager->create('Best4Mage\DPPC\Model\Shape');
           
            if (isset($files['image']) && isset($files['image']['name']) && strlen($files['image']['name'])) {
                try {
                    $baseMediaPath = 'code/Best4Mage/DPPC/view/base/web/images/';
                    $uploader = $this->_uploaderFactory->create(['fileId' => 'image']);
                    $filenameAdapter = $this->_adapterFactory->create();

                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->addValidateCallback('image', $filenameAdapter, 'validateUploadFile');
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $uploader->setAllowCreateFolders(true);

                    $mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::APP);

                    $result = $uploader->save($mediaDirectory->getAbsolutePath($baseMediaPath));


                    $image = $result['file'];
                    $absPath = $mediaDirectory->getAbsolutePath($baseMediaPath).$image;
                    $imageResized = $mediaDirectory->getAbsolutePath($baseMediaPath.'resized/').$image;

                    $imageResize = $this->_adapterFactory->create();
                    $imageResize->open($absPath);
                    $imageResize->keepAspectRatio(true);
                    $imageResize->keepFrame(false);
                    $imageResize->resize(120, 120);
                    $imageResize->save($imageResized);

                    $data['image'] = $imageResized;// $result['file'];
                } catch (\Exception $e) {
                    if ($e->getCode() == 0) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            } else {
                if (isset($data['image']) && isset($data['image']['value'])) {
                    if (isset($data['image']['delete'])) {
                        $data['image'] = null;
                        $data['delete_image'] = true;
                    } elseif (isset($data['image']['value'])) {
                        $data['image'] = $data['image']['value'];
                    } else {
                        $data['image'] = null;
                    }
                }
            }

            if (isset($data['image']) && $data['image'] != null) {
                $data['image'] = basename($data['image']);
            }

            if (isset($data['side_id']) && $data['side_id'] != '') {
                $data['side_id'] = implode(',', $data['side_id']);
            } else {
                $data['side_id'] = '';
            }

            $id = $this->getRequest()->getParam('shape_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);

            $this->_eventManager->dispatch(
                'dppc_shape_prepare_save',
                ['shape' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved this shape.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['shape_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the shape.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['shape_id' => $this->getRequest()->getParam('shape_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

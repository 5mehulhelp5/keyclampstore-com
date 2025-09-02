<?php
namespace Its\Addfee\Controller\Index;

class Addpowdercoatingcolor extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_checkoutSession;
	protected $_request;

	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
	){
		$this->_pageFactory = $pageFactory;
		$this->_request = $request;
		$this->_checkoutSession = $checkoutSession;
		$this->resultJsonFactory = $resultJsonFactory;
		return parent::__construct($context);
	}

	function execute()
	{
		/** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        
		$requestParams = $this->_request->getPost() ;
		
		if($requestParams['colorcode'] !="" )
		{
			$this->_checkoutSession->setIsSetPowderCoatingColor($requestParams['colorcode']);	
			$result['status'] = true;
		}  else {
			$result['status'] = false;
		}
		
		return $resultJson->setData($result);
	}
}
?>
<?php
namespace Its\Addfee\Controller\Index;

class Reload extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_checkoutSession;
	protected $_request;

	function __construct(
		\Magento\Framework\App\Action\Context $context,
		 \Magento\Checkout\Model\Session $checkoutSession,
		  \Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		$this->_request = $request;
		$this->_checkoutSession = $checkoutSession;
		return parent::__construct($context);
	}

	function execute()
	{		
		echo "hi";
	}
}
?>
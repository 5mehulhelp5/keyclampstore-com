<?php
namespace Its\Addfee\Controller\Index;

class Addpowdercoating extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_checkoutSession;
	protected $_request;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		 \Magento\Checkout\Model\Session $checkoutSession,
		  \Magento\Framework\App\RequestInterface $request,
		  \Magento\Quote\Model\Quote\Address\Total $total,
		 // \Magento\Quote\Model\Quote\Address\Total $total
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		$this->total=$total;
		$this->_request = $request;
		$this->_checkoutSession = $checkoutSession;
		return parent::__construct($context);
	}

	public function execute()
	{
		$requestParams = $this->_request->getPost() ;
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogProduct = $objectManager->get('\Magento\Catalog\Model\Product');
		$quoteModel = $objectManager->get('\Magento\Checkout\Model\Cart');

		if($requestParams['powder-coating'] == "on" ){
			$this->_checkoutSession->setIsSetPowderCoating(1);
			$this->_checkoutSession->setIsSetPowderCoatingColor($requestParams['powder-color']);
			
			/****************/ 
			if($requestParams['powder-coating-all']=="1"){
				$powercoatingItems = [];
				$items = $quoteModel->getQuote()->getAllVisibleItems();
				foreach($items as $item){			
					$_product = $catalogProduct->load($item->getProductId()); 
					
					if($_product->getPowder_coating_available()){

						$powercoatingItems[] = $item->getSku();
					}
					$_product->reset();
				}
				$this->_checkoutSession->setIsSetPowderCoatingItems(serialize($powercoatingItems));	
				//print_r($powercoatingItems);
				echo "false";
			}   
			/****************/
			
		} else {
			$this->_checkoutSession->setIsSetPowderCoating(0);	
			if($requestParams['powder-coating-all']=="2"){
				$this->_checkoutSession->setIsSetPowderCoatingItems("");
				echo "false";
			}		
		}
		$this->_checkoutSession->getQuote()->collectTotals()->save();
		
		//echo "hi";
	}
}
?>
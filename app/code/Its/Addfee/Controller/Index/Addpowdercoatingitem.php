<?php
namespace Its\Addfee\Controller\Index;

class Addpowdercoatingitem extends \Magento\Framework\App\Action\Action
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
	//print_r(get_class_methods($this->_request));exit;
		$requestParams = $this->_request->getPost () ;  
		$powercoatingItemsTemp = $this->_checkoutSession->getIsSetPowderCoatingItems();
		$powercoatingItems = [];
		if($powercoatingItemsTemp != "") {
			$powercoatingItems = unserialize($powercoatingItemsTemp);
		}
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$catalogProduct = $objectManager->get('\Magento\Catalog\Model\Product');
		// echo "call 123";exit;
		//print_r(get_class_methods($requestParams)); 
		$requestParams = $requestParams->toArray();
		//print_r($requestParams);exit;
		//$powercoatingItems[] = "aa";
		//print_r($powercoatingItems);
		if(isset($requestParams['item']) && isset($requestParams['active']) && isset($requestParams['itemsku'])){
				 
			//$_product = $catalogProduct->loadByAttribute('sku', $requestParams['item']);
			
			$_product = $catalogProduct->load($requestParams['item']);
			//echo $_product->getId();exit;
			if($_product->getPowder_coating_available()){
				
			} else {
				echo "2";exit;
			}
						
			if($requestParams['active']=="1") { 
				$powercoatingItems[] = (string)$requestParams['itemsku'];
			} else if($requestParams['active']=="0") {
				$tempArr = [];
				foreach($powercoatingItems as $k=>$v){
					if($v==$requestParams['itemsku']){
						
					} else {
						$tempArr[] = $v;
					}
				}
				$powercoatingItems = $tempArr;
			}
		}
		 
		$this->_checkoutSession->setIsSetPowderCoatingItems(serialize($powercoatingItems));	
		$powercoatingItemsTemp = $this->_checkoutSession->getIsSetPowderCoatingItems();
		
		$powercoatingItems = unserialize($powercoatingItemsTemp);
		//print_r($powercoatingItems);
		if(count($powercoatingItems )>0){
			echo "1";
		} else {
			echo "0";
		}
		exit;
		//print_r($requestParams);
		//echo $requestParams['powder-coating'];
		//echo $requestParams['powder-color'];
		/*if($requestParams['powder-coating']=="on"){
			$this->_checkoutSession->setIsSetPowderCoatingItems(1);			
		} else {
			$this->_checkoutSession->setIsSetPowderCoating(0);		
		}*/
		
		//echo "hi";
	}
}
?>
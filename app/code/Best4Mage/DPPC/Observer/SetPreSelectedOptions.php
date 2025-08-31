<?php

namespace Best4Mage\DPPC\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
 
class SetPreSelectedOptions implements ObserverInterface
{
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;
    /**
     * @var \Best4Mage\DPPC\Helper\Options
     */
    protected $_optionHelper;


    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Best4Mage\DPPC\Helper\Options $optionHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_productRepository = $productRepository;
        $this->_optionHelper = $optionHelper;
    }

    public function execute(EventObserver $observer)
    {
        $params = $observer->getParams();

        $buyRequest = $params->getBuyRequest();

        if ($buyRequest) {
            $productId = $buyRequest->getProduct();

            $product = $this->_productRepository->getById($productId, false, $this->_storeManager->getStore()->getId());

            $this->_optionHelper->prepareProductOptions($product, $buyRequest);
        }
    }
}

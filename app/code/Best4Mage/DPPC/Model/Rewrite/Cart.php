<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Best4Mage\DPPC\Model\Rewrite;

class Cart extends \Magento\Checkout\Model\Cart
{

    /**
     * Load DPPC HElper
     *
     * @var Best4Mage\DPPC\Helper\Data
     */
    protected $dppcHelper;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\ResourceModel\Cart $resourceCart
     * @param Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param ProductRepositoryInterface $productRepository
     * @param array $data
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\ResourceModel\Cart $resourceCart,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Best4Mage\DPPC\Helper\Data $dppcHelper
    ) {
        $this->dppcHelper = $dppcHelper;
        parent::__construct($eventManager,$scopeConfig,$storeManager,$resourceCart,$checkoutSession,$customerSession,$messageManager,$stockRegistry,$stockState,$quoteRepository,$productRepository);
    }

    /**
     * Add product to shopping cart (quote)
     *
     * @param int|Product $productInfo
     * @param \Magento\Framework\DataObject|int|array $requestInfo
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addProduct($productInfo, $requestInfo = null)
    {
        $product = $this->_getProduct($productInfo);
        $productId = $product->getId();
        
        if ($productId) {
            $request = $this->getQtyRequest($product, $requestInfo);
            
            try {
                if($this->dppcHelper->isEnable() && $product->getDppcProductEnable() && !array_key_exists('side_options',$requestInfo) && !array_key_exists('selected_shape',$requestInfo) && !array_key_exists('selected_shape_formula',$requestInfo)){
                    
                    $result = "You need to choose options for your item.";   
                    
                }else{
                    $this->_eventManager->dispatch(
                        'checkout_cart_product_add_before',
                        ['info' => $requestInfo, 'product' => $product]
                    );
                    $result = $this->getQuote()->addProduct($product, $request);    
                }
                
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->_checkoutSession->setUseNotice(false);
                $result = $e->getMessage();
            }
            /**
             * String we can get if prepare process has error
             */
            if (is_string($result)) {
                if ($product->hasOptionsValidationFail()) {
                    $redirectUrl = $product->getUrlModel()->getUrl(
                        $product,
                        ['_query' => ['startcustomization' => 1]]
                    );
                } else {
                    $redirectUrl = $product->getProductUrl();
                }
                $this->_checkoutSession->setRedirectUrl($redirectUrl);
                if ($this->_checkoutSession->getUseNotice() === null) {
                    $this->_checkoutSession->setUseNotice(true);
                }
                throw new \Magento\Framework\Exception\LocalizedException(__($result));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('The product does not exist.'));
        }

        $this->_eventManager->dispatch(
            'checkout_cart_product_add_after',
            ['quote_item' => $result, 'product' => $product]
        );
        $this->_checkoutSession->setLastAddedProductId($productId);
        return $this;
    }

    private function getQtyRequest($product, $request = 0)
    {
        $request = $this->_getProductRequest($request);
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $minimumQty = $stockItem->getMinSaleQty();
        //If product quantity is not specified in request and there is set minimal qty for it
        if ($minimumQty
            && $minimumQty > 0
            && !$request->getQty()
        ) {
            $request->setQty($minimumQty);
        }

        return $request;
    }
}
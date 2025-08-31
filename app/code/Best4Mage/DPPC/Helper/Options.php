<?php
/**
 * Best4Mage - DPPC
 * @author Best4Mage
 */
?>
<?php
namespace Best4Mage\DPPC\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Options extends AbstractHelper
{

    protected $product;

    /**
     * @var \Magento\Framework\Registry
     */

    protected $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */

    protected $request;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyCode;

    /**
     * @var \Best4Mage\DPPC\Model\Unit
     */
    protected $unit;

    /**
     * @param \Magento\Framework\Registry $registry,
     * @param \Magento\Framework\App\RequestInterface $request,
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper,
     * @param \Magento\Checkout\Model\Session $checkoutSession,
     * @param \Magento\Store\Model\StoreManagerInterface $registry,
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory,
     * @param \Best4Mage\DPPC\Model\Unit $unit
     */

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Best4Mage\DPPC\Model\Unit $unit
    ) {
        $this->registry = $registry;
        $this->request = $request;
        $this->jsonHelper = $jsonHelper;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->currencyCode = $currencyFactory->create();
        $this->unit = $unit;
    }
    

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function getConfig()
    {

        $_config['measurement'] = $this->unit->setProduct($this->getProduct())->getConfig();
                
        $_config['currentProduct'] = $this->getDefaultProductId();
       
        $_config['productType'] = $this->getProduct()->getTypeId();
        
        $_config['productId'] = $this->getProduct()->getId();

        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrencyCode();

        $currency = $this->currencyCode->load($currentCurrency);
        
        $_config['currencySymbol'] = $currency->getCurrencySymbol();

        $_config['preSelectedDPPCValues'] = $this->getProduct()->getPreSelectedDPPCValues();
        
        return $_config;
    }

    public function getDefaultProductId()
    {
        if ($this->getProduct()->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
           // return $this->request->getActionName();
            if ($this->request->getActionName() == 'configure') {
                $_quoteItem = $this->checkoutSession->getQuote()->getItemById($this->request->getParam('id', false));
               
                if ($_quoteItem && $_quoteItem->getId()) {
                    foreach ($_quoteItem->getChildren() as $child) {
                        return $child->getProduct()->getId();
                    }
                }
            }
        }
        
        return  $this->getProduct()->getId();
    }

    public function prepareProductOptions($product, $buyRequest)
    {
        $optionValues = [];
        $productId = $product->getId();
        $selectedShape = $buyRequest->getSelectedShape();
        $shapeFormula = $buyRequest->getSelectedShapeFormula();
        $shapeOptions = $buyRequest->getShapeOptions();
        $sideOptions = $buyRequest->getSideOptions();
        $optionAreaInput = $buyRequest->getData('option-area-input');
        
        $selectedSideOptions = $sideOptions[$productId][$selectedShape];

        $optionValues['selected_shape_formula'] =  $shapeFormula;
        $optionValues['selected_shape'] =  $selectedShape;
        $optionValues['side_options'] =  $selectedSideOptions;
        $optionValues['option_area_input'] =  $optionAreaInput;

        $product->setPreSelectedDPPCValues($optionValues);

        return $this;
    }
}

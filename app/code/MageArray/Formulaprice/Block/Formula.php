<?php
namespace MageArray\Formulaprice\Block;

class Formula extends \Magento\Framework\View\Element\Template
{

    /**
     * @var [type]
     */
    protected $_coreRegistry;

    /**
     * @var [type]
     */
    protected $_objectManager;

    /**
     * @var [type]
     */
    protected $_dataHelper;

    /**
     * @var [type]
     */
    protected $_calculation;

    /**
     * @var [type]
     */
    protected $_taxHelper;

    /**
     * @var [type]
     */
    protected $productloader;

    /**
     * Constructor data.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MageArray\Formulaprice\Helper\Data $dataHelper
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Catalog\Model\ProductFactory $productloader
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageArray\Formulaprice\Helper\Data $dataHelper,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_objectManager = $objectManager;
        $this->_dataHelper = $dataHelper;
        $this->_calculation = $calculation;
        $this->_taxHelper = $taxHelper;
        $this->productloader = $productloader;
    }

    /**
     * Get Json
     *
     * @return void
     */
    public function getJson()
    {
        $product = $this->_coreRegistry->registry('current_product');
        $addQty =  $this->_dataHelper->getAddQty();
        $pid = $product->getId();
        $productColl = $this->productloader->create()->load($pid);

        $attributes = $productColl->getAttributes();
        $attr = [];
        foreach ($attributes as $attribute) {
            $attrType = $attribute->getBackendType();
            if ($attrType != 'int' && $attrType != 'decimal' && $attrType != 'varchar') {
                continue;
            }
            $attrCode = $attribute->getAttributeCode();
            $value = $productColl->getData($attrCode);
            if (is_string($value) && ($attrType == 'int' || $attrType == 'decimal')) {
                $attr[$attrCode] = $value;
            }

            if (is_numeric($value) && $attrType == 'varchar') {
                $attr[$attrCode] = $value;
            }
        }

        $options = $productColl->getOptions();
        $optionsArr = [];
        foreach ($options as $option) {
            $id = $option->getId();
            $optionsArr[$id]['id'] = $option->getId();
            $optionsArr[$id]['sku'] = $option->getSku();
            $optionsArr[$id]['price'] = ($option->getPrice()) ? number_format($option->getPrice(), 2) : 0;
            $optionsArr[$id]['type'] = $option->getType();
            $optionsArr[$id]['label'] = $option->getTitle();
            if ($option->getValues()) {
                foreach ($option->getValues() as $value) {
                    $oId = $value->getOptionTypeId();
                    $optionsArr[$id]['values'][$oId]['id'] = $oId;
                    $optionsArr[$id]['values'][$oId]['sku'] = $value->getSku();
                    $optionsArr[$id]['values'][$oId]['price'] = ($value->getPrice()) ?
                    number_format($value->getPrice(), 2) : 0;
                }
            }
        }

        $finalOp = [];
        $finalVal = [];
        $checkMin = [];
        $checkMax = [];
        $custVars = $productColl->getFormulaPriceVariable();
        $custVar = explode(';', $custVars ?? '');
        foreach ($custVar as $var) {
            $label = explode('=>', $var);
            if (isset($label[0]) && isset($label[1])) {
                $finalOp[strtolower(trim((string)$label[0]))] = trim((string)$label[1]);
            }
        }

        $custVals = $productColl->getAssignCustomOptionValue();
        if ($custVals != '') {
            $custVal = explode(';', $custVals);
            foreach ($custVal as $val) {
                $valLabel = explode('=>', $val);
                if (isset($valLabel[0]) && isset($valLabel[1])) {
                    $finalVal[strtolower(trim((string)$valLabel[0]))] = trim((string)$valLabel[1]);
                }
            }
        }

        $custMin = $productColl->getFormulaPriceMin();
        if ($custMin != '') {
            $custMin = explode(';', $custMin);
            foreach ($custMin as $min) {
                $minVal = explode('=>', $min);
                if (isset($minVal[0]) && isset($minVal[1])) {
                    $minTitle = "{" . strtolower(trim((string)$minVal[0])) . "}";
                    $checkMin[$minTitle] = trim((string)$minVal[1]);
                }
            }
        }

        $custMax = $productColl->getFormulaPriceMax();
        if ($custMax) {
            $custMax = explode(';', $custMax);
            foreach ($custMax as $max) {
                $maxVal = explode('=>', $max);
                if (isset($maxVal[0]) && isset($maxVal[1])) {
                    $maxTitle = "{" . strtolower(trim((string)$maxVal[0])) . "}";
                    $checkMax[$maxTitle] = trim((string)$maxVal[1]);
                }
            }
        }

        $finalEquation = $productColl->getFormulaPriceFinal();

        $staticEquation = $productColl->getStaticVariableEquation();
        if ($staticEquation != '') {
            $staticEquations = explode(';', $staticEquation);
            foreach ($staticEquations as $staticEquation) {
                $staticEqua = explode('=>', $staticEquation);
                if (isset($staticEqua[0]) && isset($staticEqua[1])) {
                    $defineVar = "{" . strtolower(trim((string)$staticEqua[0])) . "}";
                    $defineEqu = trim((string)$staticEqua[1]);
                    if (strpos($finalEquation, $defineVar) !== false) {
                        $finalEquation = str_replace($defineVar, $defineEqu, $finalEquation);
                    }
                }
            }
        }

        $staticVariables = $productColl->getStaticFixedVariable();
        $staticVar = [];
        if ($staticVariables != '') {
            $staticVariables = explode(';', $staticVariables);
            foreach ($staticVariables as $staticVariable) {
                $staticVars = explode('=>', $staticVariable);
                if (isset($staticVars[0]) && isset($staticVars[1])) {
                    $staticVar[strtolower(trim((string)$staticVars[0]))] = trim((string)$staticVars[1]);
                }
            }
        }

        $extraFormula = $productColl->getExtraPriceFormula();

        foreach ($staticVar as $key => $val) {
            $key = "{" . $key . "}";
            if (strpos($finalEquation, $key) !== false) {
                $finalEquation = str_replace($key, $val, $finalEquation);
            }
            if ($extraFormula != '' && strpos($extraFormula, $key) !== false) {
                $extraFormula = str_replace($key, $val, $extraFormula);
            }
        }

        foreach ($attr as $akey => $aval) {
            $akey = "{" . $akey . "}";
            if (strpos($finalEquation ?? '', $akey) !== false) {
                $finalEquation = str_replace($akey, $aval, $finalEquation);
            }

            if ($extraFormula != '' && strpos($extraFormula, $akey) !== false) {
                $extraFormula = str_replace($akey, $aval, $extraFormula);
            }
        }

        $data = [];
        $data['options'] = $optionsArr;
        $data['enable'] = $productColl->getFormulaPriceEnable();
        $data['finalEquation'] = $finalEquation;
        $data['allLabel'] = $finalOp;
        $data['allValue'] = $finalVal;
        $data['minError'] = $productColl->getFormulaPriceMinVar();
        $data['maxError'] = $productColl->getFormulaPriceMaxVar();
        $data['min'] = $checkMin;
        $data['max'] = $checkMax;
        $data['extraFormula'] = $extraFormula;
        $data['addQty'] = $addQty;
        $data['proPrice'] = $productColl->getPrice();
        $data['taxVat'] = $this->getTaxVat($product);
        $data['isExcludingPrice'] = $this->_taxHelper->displayPriceExcludingTax();
        return json_encode($data);
    }

    /**
     * Get TaxVat
     *
     * @param [type] $product
     * @return void
     */
    public function getTaxVat($product)
    {
        $store=$this->_storeManager->getStore();
        $request = $this->_calculation->getRateRequest(null, null, null, $store);
        $taxClassId = $product->getTaxClassId();
        return $percent = $this->_calculation->getRate($request->setProductClassId($taxClassId));
    }
}

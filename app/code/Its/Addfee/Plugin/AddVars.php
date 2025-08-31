<?php
declare(strict_types=1);

namespace Its\Addfee\Plugin;

class AddVars
{
    /**
     * OrderSetTemplateVarsBefore constructor.
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * @return void
     */
    public function beforeSetTemplateVars($subject, array $vars) {
        
        //get order
        if (isset($vars['order']) && $vars['order'] instanceof \Magento\Sales\Model\Order) {
            $order = $vars['order'];
            $colorcode = $order->getPowdercoatingColorcode();
            $extrafee = $this->pricingHelper->currencyByStore($order->getFee(), $order->getStoreId(), true, false);
            $items = '';
            if (!empty($order->getPowdercoatingItems())) {
                $items = implode(',', unserialize($order->getPowdercoatingItems()));
            };

            if($colorcode){
                $vars['CustomVariable1'] = "Powdercoating selected color : $colorcode"." and fee is $extrafee"." and items are ".$items;
            }else{
                $vars['CustomVariable1'] = "";
            }
        }
       

        return [$vars];
    }
}

<?php

namespace Best4Mage\DPPC\Block\Product\View;

use Magento\Catalog\Model\Product;

/**
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Options extends \Magento\Catalog\Block\Product\View\Options
{
    
    /**
     * Get price configuration
     *
     * @param \Magento\Catalog\Model\Product\Option\Value|\Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $optionPrice = $this->pricingHelper->currency($option->getPrice(true), false, false);
        $optionPercent = $this->pricingHelper->currency($option->getPrice(), false, false);
        $data = [
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->pricingHelper->currency($option->getRegularPrice(), false, false),
                    'percent' => $option->getPriceType()== 'percent' ? $optionPercent : '',
                    'adjustments' => [],
                ],
                'basePrice' => [
                    'amount' => $this->_catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                    'percent' => $option->getPriceType()== 'percent' ? $optionPercent : '',
                    
                ],
                'finalPrice' => [
                    'amount' => $this->_catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                    'percent' => $option->getPriceType()== 'percent' ? $optionPercent : '',
                ],
            ],
            'type' => $option->getPriceType(),
            'name' => $option->getTitle()
        ];
        
        return $data;
    }
}

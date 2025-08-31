<?php
namespace Best4Mage\DPPC\Model\Config\Source;

class Configureprice implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('On Product Price'),
                'value' => 0,
            ],
            [
                'label' => __('On DPPC Price'),
                'value' => 1,
            ],
        ];
    }
}

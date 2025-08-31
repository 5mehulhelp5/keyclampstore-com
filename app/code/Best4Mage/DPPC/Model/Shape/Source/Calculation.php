<?php
namespace Best4Mage\DPPC\Model\Shape\Source;

class Calculation implements \Magento\Framework\Data\OptionSourceInterface
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
                'label' => __('Surface'),
                'value' => 1,
            ],
            [
                'label' => __('Volume'),
                'value' => 0,
            ]
        ];
    }
}

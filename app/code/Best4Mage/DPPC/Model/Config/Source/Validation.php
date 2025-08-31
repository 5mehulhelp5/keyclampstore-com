<?php
namespace Best4Mage\DPPC\Model\Config\Source;

class Validation implements \Magento\Framework\Data\OptionSourceInterface
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
                'label' => __('Default'),
                'value' => 0,
            ],
            [
                'label' => __('Field validation for all Shape'),
                'value' => 1,
            ],
            [
                'label' => __('Field Validation for each shape'),
                'value' => 2,
            ],
        ];
    }
}

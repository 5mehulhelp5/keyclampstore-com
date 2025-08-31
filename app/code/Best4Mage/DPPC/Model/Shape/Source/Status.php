<?php
namespace Best4Mage\DPPC\Model\Shape\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
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
                'label' => __('Enabled'),
                'value' => 1,
            ],
            [
                'label' => __('Disabled'),
                'value' => 0,
            ]
        ];
    }
}

<?php
namespace Best4Mage\DPPC\Model\Config\Source;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

class Unit extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * @var OptionFactory
     */

    protected $optionFactory;

    /**
     * Retrieve all options array
     * @return array
     */

    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('-- Please Select --'),'value' => ''],
            ['label' => __('Millimeter'),'value' => 'Millimeter'],
            ['label' => __('Centimeter'),'value' => 'Centimeter'],
            ['label' => __('Meter'),'value' => 'Meter'],
            ['label' => __('Inch'),'value' => 'Inch'],
            ['label' => __('Foot'),'value' => 'Foot'],
        ];

        return $this->_options;
    }

    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            ['value' => '','label' => __('-- Please Select --') ],
            ['value' => 'Millimeter','label' => __('Millimeter') ],
            ['value' => 'Centimeter','label' => __('Centimeter') ],
            ['value' => 'Meter','label' => __('Meter') ],
            ['value' => 'Inch','label' => __('Inch') ],
            ['value' => 'Foot','label' => __('Foot') ],
        ];

        return $return;
    }

    /**
     * Get options in 'key=>value' format
     * @return array
     */
    public function toArray()
    {
        $return = [
            '' => __('Choose Product...'),
            'Millimeter' => __('Millimeter'),
            'Centimeter' => __('Centimeter'),
            'Meter' => __('Meter'),
            'Inch' => __('Inch'),
            'Foot' => __('Foot'),
        ];

        return $return;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => true,
                'default' => null,
                'extra' => null,
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'DPPC Measurement Unit ' . $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_eavAttrEntity->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Set attribute instance
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }
}

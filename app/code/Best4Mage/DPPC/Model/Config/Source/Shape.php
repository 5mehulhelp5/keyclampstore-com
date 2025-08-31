<?php
namespace Best4Mage\DPPC\Model\Config\Source;

class Shape extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Best4Mage\DPPC\Model\ShapeFactory
     */
    protected $_shapeFactory;

    /**
     * @var Array
     */
    protected $_allShapes = null;

    public function __construct(
        \Best4Mage\DPPC\Model\ShapeFactory $shapeFactory
    ) {
        $this->_shapeFactory = $shapeFactory;
    }

    protected function _getAllShapes()
    {

        if (is_null($this->_allShapes)) {
            $shapeCollection = $this->_shapeFactory->create()->getCollection()->addFieldToFilter('status', 1);

            if (count($shapeCollection)) {
                foreach ($shapeCollection as $shape) {
                    $this->_allShapes[$shape->getId()] = $shape->getTitle();
                }
            }
        }
        return $this->_allShapes;
    }


    /**
     * Get options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            [
                'label' => __('---No Selection---'),
                'value' => '0'
            ],
        ];

        if (!is_null($shapes = $this->_getAllShapes())) {
            foreach ($shapes as $value => $label) {
                array_push(
                    $this->_options,
                    [
                        'label' => $label,
                        'value' => (string)$value
                    ]
                );
            }
        }

        return $this->_options;
    }

    public function getOptionArray()
    {
        $_options = [];
        $index = 0;
        foreach ($this->getAllOptions() as $option) {
            $_options[$index]['value'] = (string)$option['value'];
            $_options[$index]['label'] = $option['label'];
            $index++;
        }
        return $_options;
    }


    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            [
                'value' => '0',
                'label' => __('---No Selection---')
            ],
        ];

        if (!is_null($shapes = $this->_getAllShapes())) {
            foreach ($shapes as $value => $label) {
                array_push(
                    $return,
                    [
                        'value' => (string)$value,
                        'label' => $label
                    ]
                );
            }
        }
        return $return;
    }

    /**
     * Get options in 'key=>value' format
     * @return array
     */
    public function toArray()
    {
        $return = [
            '0' => __('---No Selection---')
        ];

        if (!is_null($shapes = $this->_getAllShapes())) {
            foreach ($shapes as $value => $label) {
                array_push(
                    $return,
                    [
                        (string)$value => $label
                    ]
                );
            }
        }
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
                'comment' => 'DPPC product Shape' . $attributeCode . ' column',
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

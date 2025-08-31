<?php
namespace Best4Mage\DPPC\Model\Shape\Source;

class Sides extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Best4Mage\DPPC\Model\SideFactory
     */
    protected $_sideFactory;

    /**
     * @var Array
     */
    protected $_allSides = null;

    public function __construct(
        \Best4Mage\DPPC\Model\SideFactory $sideFactory
    ) {
        $this->_sideFactory = $sideFactory;
    }

    protected function _getAllSides()
    {

        if (is_null($this->_allSides)) {
            $sideCollection = $this->_sideFactory->create()->getCollection();

            if (count($sideCollection)) {
                foreach ($sideCollection as $side) {
                    $this->_allSides[$side->getCode()] = $side->getTitle().' ('.$side->getCode().')';
                }
            }
        }
        return $this->_allSides;
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
                'value' => ''
            ],
        ];

        if (!is_null($sides = $this->_getAllSides())) {
            foreach ($sides as $value => $label) {
                array_push(
                    $this->_options,
                    [
                        'label' => $label,
                        'value' => $value
                    ]
                );
            }
        }

        return $this->_options;
    }


    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        $return = [
            [
                'value' => '',
                'label' => __('---No Selection---')
            ],
        ];

        if (!is_null($sides = $this->_getAllSides())) {
            foreach ($sides as $value => $label) {
                array_push(
                    $return,
                    [
                        'value' => $value,
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
            '' => __('---No Selection---')
        ];

        if (!is_null($sides = $this->_getAllSides())) {
            foreach ($sides as $value => $label) {
                array_push(
                    $return,
                    [
                        $value => $label
                    ]
                );
            }
        }
        return $return;
    }
}

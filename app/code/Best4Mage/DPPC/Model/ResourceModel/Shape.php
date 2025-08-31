<?php
namespace Best4Mage\DPPC\Model\ResourceModel;

/**
 * DPPC Shape mysql resource
 */
class Shape extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dppc_shape', 'shape_id');
    }

    /**
     * Process shape data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        // check if the min_max_value is valid or not
        if (!$this->_isValidMinMaxValue($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The {min}:{max} value pair is not valid. Please refer example. No. of pair should match the no. of selected sides. Value must only contain "0-9" , ":" & ","')
            );
        } else {
            $minMaxValue = $object->getData('min_max_value');
            $minMaxValue = trim($minMaxValue, " \t\n\r\0\x0B:,");
            $object->setData('min_max_value', $minMaxValue);
        }

        // set created_at value
        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }

        // set updated_at value
        $object->setUpdatedAt($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Check whether the min-max value is valid
     * @param AbstractModel $object
     * @return bool
     */
    protected function _isValidMinMaxValue($object)
    {

        $minMaxValue = $object->getData('min_max_value');

        if ($minMaxValue) {
            $selectedSides = $object->getData('side_id');

            $minMaxValue = trim($minMaxValue, " \t\n\r\0\x0B:,");
            $minMaxValue = explode(',', $minMaxValue);
            $selectedSides = explode(',', trim($selectedSides));

            if (count($minMaxValue) != count($selectedSides)) {
                return false;
            }

            foreach ($minMaxValue as $key => $minMaxPair) {
                // if value has letters
                if (preg_match('/[a-zA-Z]/', $minMaxPair)) {
                    return false;
                }

                // if value is in number:number format or not
                if (!preg_match('/^[0-9]+:[0-9]+$/', $minMaxPair)) {
                    return false;
                }
            }
        }
        return true;
    }
}

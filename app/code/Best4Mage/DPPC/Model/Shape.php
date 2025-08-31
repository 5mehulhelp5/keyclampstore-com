<?php

namespace Best4Mage\DPPC\Model;

use Best4Mage\DPPC\Api\Data\ShapeInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Shape extends \Magento\Framework\Model\AbstractModel implements ShapeInterface, IdentityInterface
{
    /**
     * DPPC shape cache tag
     */
    const CACHE_TAG = 'dppc_shape';

    /**
     * @var string
     */
    protected $_cacheTag = 'dppc_shape';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'dppc_shape';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Best4Mage\DPPC\Model\ResourceModel\Shape');
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::SHAPE_ID);
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get side_id
     *
     * @return string|null
     */
    public function getSideId()
    {
        return $this->getData(self::SIDE_ID);
    }

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * Get min_max_value
     *
     * @return string
     */
    public function getMinMaxValue()
    {
        return $this->getData(self::MIN_MAX_VALUE);
    }

    /**
     * Get calculation_type
     *
     * @return string
     */
    public function getCalculationType()
    {
        return $this->getData(self::CALCULATION_TYPE);
    }

    /**
     * Get formula
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->getData(self::FORMULA);
    }

    /**
     * Get sort_order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Get updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get active
     *
     * @return bool|null
     */
    public function getStatus()
    {
        return (bool) $this->getData(self::STATUS);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setId($id)
    {
        return $this->setData(self::SHAPE_ID, $id);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set side_id
     *
     * @param string $sideId
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setSideId($sideId)
    {
        return $this->setData(self::SIDE_ID, $sideId);
    }

    /**
     * Set image
     *
     * @param string $image
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Set min_max_value
     *
     * @param string $minMaxValue
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setMinMaxValue($minMaxValue)
    {
        return $this->setData(self::MIN_MAX_VALUE, $minMaxValue);
    }

    /**
     * Set calculation_type
     *
     * @param string $calculationType
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setCalculationType($calculationType)
    {
        return $this->setData(self::CALCULATION_TYPE, $calculationType);
    }

    /**
     * Set formula
     *
     * @param string $formula
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setFormula($formula)
    {
        return $this->setData(self::FORMULA, $formula);
    }

    /**
     * Set sort_order
     *
     * @param int $sortOrder
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Set status
     *
     * @param int|bool $status
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}

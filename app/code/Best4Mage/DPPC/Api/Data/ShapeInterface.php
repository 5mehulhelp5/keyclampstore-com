<?php
namespace Best4Mage\DPPC\Api\Data;

interface ShapeInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SHAPE_ID      = 'shape_id';
    const TITLE         = 'title';
    const SIDE_ID       = 'side_id';
    const IMAGE         = 'image';
    const MIN_MAX_VALUE = 'min_max_value';
    const CALCULATION_TYPE = 'calculation_type';
    const SORT_ORDER    = 'sort_order';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';
    const STATUS        = 'status';
    const FORMULA       = 'formula';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get side_id
     *
     * @return string|null
     */
    public function getSideId();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get min_max_value
     *
     * @return string
     */
    public function getMinMaxValue();

    /**
     * Get calculation_type
     *
     * @return string
     */
    public function getCalculationType();

    /**
     * Get sort_order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get status
     *
     * @return bool|null
     */
    public function getStatus();

    /**
     * Get formula
     *
     * @return string|null
     */
    public function getFormula();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setId($id);

     /**
      * Set side_id
      *
      * @param string $sideId
      * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
      */
    public function setSideId($sideId);

    /**
     * Set title
     *
     * @param string $title
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setTitle($title);

    /**
     * Set image
     *
     * @param string $image
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setImage($image);

     /**
      * Set min_max_value
      *
      * @param string $minMaxValue
      * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
      */
    public function setMinMaxValue($minMaxValue);

    /**
     * Set min_max_value
     *
     * @param string $calculationType
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setCalculationType($calculationType);

    /**
     * Set sort_order
     *
     * @param int $sortOrder
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Set formula
     *
     * @param int $formula
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setFormula($formula);

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updated_at
     *
     * @param string $updatedAt
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set status
     *
     * @param int|bool $status
     * @return \Best4Mage\DPPC\Api\Data\ShapeInterface
     */
    public function setStatus($status);
}

<?php
namespace Best4Mage\DPPC\Api\Data;

interface SideInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SIDE_ID       = 'side_id';
    const TITLE         = 'title';
    const CODE          = 'code';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setTitle($title);

    /**
     * Set content
     *
     * @param string $content
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setCode($code);
}

<?php

namespace Best4Mage\DPPC\Model;

use Best4Mage\DPPC\Api\Data\SideInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Side extends \Magento\Framework\Model\AbstractModel implements SideInterface, IdentityInterface
{

    /**
     * DPPC Side cache tag
     */
    const CACHE_TAG = 'dppc_side';

    /**
     * @var string
     */
    protected $_cacheTag = 'dppc_side';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'dppc_side';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Best4Mage\DPPC\Model\ResourceModel\Side');
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
        return $this->getData(self::SIDE_ID);
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
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setId($id)
    {
        return $this->setData(self::SIDE_ID, $id);
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Set code
     *
     * @param string $code
     * @return \Best4Mage\DPPC\Api\Data\SideInterface
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }
}

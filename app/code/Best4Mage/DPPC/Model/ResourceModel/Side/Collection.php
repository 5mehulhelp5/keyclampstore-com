<?php

namespace Best4Mage\DPPC\Model\ResourceModel\Side;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'side_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Best4Mage\DPPC\Model\Side', 'Best4Mage\DPPC\Model\ResourceModel\Side');
    }
}

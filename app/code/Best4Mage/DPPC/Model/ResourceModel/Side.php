<?php
namespace Best4Mage\DPPC\Model\ResourceModel;

/**
 * DPPC Side mysql resource
 */
class Side extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dppc_side', 'side_id');
    }
}

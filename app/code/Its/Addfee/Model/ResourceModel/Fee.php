<?php
namespace Its\Addfee\Model\ResourceModel;

class Fee extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('powdercoating_fees', 'id');
    }

  
}

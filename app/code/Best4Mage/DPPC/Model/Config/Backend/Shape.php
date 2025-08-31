<?php
 
namespace Best4Mage\DPPC\Model\Config\Backend;

class Shape extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
     
    public function beforeSave($object)
    {
        
        $_code = $this->getAttribute()->getName();
        if ($_code == 'dppc_product_shapes') {
             $_data = $object->getData($_code);
             
            if (!is_array($_data)) {
                $_data = [];
            }
             
             $object->setData($_code, implode(',', $_data));
        }
          
        return $this;
    }
}

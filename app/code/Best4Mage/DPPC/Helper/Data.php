<?php
/**
 * Best4Mage - DPPC
 * @author Best4Mage
 */
?>
<?php

namespace Best4Mage\DPPC\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const XML_PATH_DPPC = 'dppc/';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnable($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DPPC .'general_settings/enable', $storeId);
    }

    public function configurePrice($storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_DPPC.'general_settings/configure_price', $storeId);
    }
}

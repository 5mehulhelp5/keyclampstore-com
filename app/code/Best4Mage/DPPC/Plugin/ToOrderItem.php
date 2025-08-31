<?php
namespace Best4Mage\DPPC\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
 
class ToOrderItem
{
    /**
     * aroundConvert
     *
     * @param QuoteToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $data
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        // Get Order Item
        $orderItem = $proceed($item, $data);
        // Get Quote Item's additional Options
        $additionalOptions = $item->getOptionByCode('additional_options');
 
        // Check if there is any additional options in Quote Item
        if ($additionalOptions) {
            // Get Order Item's other options
            $options = $orderItem->getProductOptions();
            // Set additional options to Order Item


            //if (version_compare($this->getMagentoVersion(), '2.2.0', '>=')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $serializer = $objectManager->create('Magento\Framework\Serialize\Serializer\Json');
                $options['additional_options'] = $serializer->unserialize($additionalOptions->getValue());
            // } else {
            //     $options['additional_options'] = unserialize($additionalOptions->getValue());
            // }
            
            $orderItem->setProductOptions($options);
        }
 
        return $orderItem;
    }

    public function getMagentoVersion()
    {
        if (defined('AppInterface::VERSION')) {
            return AppInterface::VERSION;
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productMetadata = $objectManager->create('Magento\Framework\App\ProductMetadataInterface');
            return $productMetadata->getVersion();
        }
    }
}

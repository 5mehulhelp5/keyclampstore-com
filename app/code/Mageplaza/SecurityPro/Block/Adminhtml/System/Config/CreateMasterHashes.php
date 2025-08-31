<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SeoUrl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SecurityPro\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Reindex
 * @package Mageplaza\SeoUrl\Block\Adminhtml\System\Config
 */
class CreateMasterHashes extends Field
{
    /**
     * Get the button Run
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $url = "'" . $this->_urlBuilder->getUrl('mpsecurity/filechange/action', ['id' => 'create_master']) . "'";

        return '<button onclick="location.href=' . $url . '" type="button">' . __('Reindex') . '</button>';
    }
}

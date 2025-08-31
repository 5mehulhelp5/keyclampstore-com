<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SecurityPro
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SecurityPro\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class FileChange
 * @package Mageplaza\SecurityPro\Block\Adminhtml
 */
class FileChange extends Container
{
    /**
     * FileChange constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_fileChange';
        $this->_blockGroup = 'Mageplaza_SecurityPro';
        $this->_headerText = __('File Change Log');

        parent::_construct();
    }
}

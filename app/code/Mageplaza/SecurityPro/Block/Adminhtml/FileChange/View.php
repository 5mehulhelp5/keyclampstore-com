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

namespace Mageplaza\SecurityPro\Block\Adminhtml\FileChange;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class View
 * @package Mageplaza\SecurityPro\Block\Adminhtml\FileChange
 */
class View extends Container
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_fileChange';
        $this->_blockGroup = 'Mageplaza_SecurityPro';
        $this->_headerText = __('File Change Log');

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
    }
}

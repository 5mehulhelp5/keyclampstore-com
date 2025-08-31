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

namespace Mageplaza\SecurityPro\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class ActionLog
 * @package Mageplaza\SecurityPro\Model
 */
class ActionLog extends AbstractModel
{
    /**
     * Initialize model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(ResourceModel\ActionLog::class);
    }
}

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

namespace Mageplaza\SecurityPro\Plugin\Console\Command;

/**
 * Class Reset
 * @package Mageplaza\SecurityPro\Plugin\Console\Command
 */
class Reset
{
    /**
     * @param \Mageplaza\Security\Console\Command\Reset $reset
     */
    public function beforeExecute(\Mageplaza\Security\Console\Command\Reset $reset)
    {
        $reset->pathsReset['awaymode'] = 'security/away_mode/enabled';
    }
}

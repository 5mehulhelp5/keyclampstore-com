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

namespace Mageplaza\SecurityPro\Plugin;

use Exception;
use Mageplaza\Security\Helper\ErrorProcessor;
use Mageplaza\SecurityPro\Helper\Data;

/**
 * Class Login
 * @package Mageplaza\Security\Plugin
 */
class Login
{
    const ERROR_CODE = 'MAGEPLAZA_SECURITY_401';

    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var ErrorProcessor
     */
    protected $errorHelper;

    /**
     * Login constructor.
     *
     * @param Data $helper
     * @param ErrorProcessor $errorHelper
     */
    public function __construct(
        Data $helper,
        ErrorProcessor $errorHelper
    ) {
        $this->_helper     = $helper;
        $this->errorHelper = $errorHelper;
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Auth\Login $login
     * @param $page
     *
     * @return null
     * @throws Exception
     */
    public function afterExecute(\Magento\Backend\Controller\Adminhtml\Auth\Login $login, $page)
    {
        if ($this->_helper->isEnabled()
            && $this->_helper->getConfigAwayMode('enabled')
            && ($login->getRequest()->getModuleName() !== 'mpsecurity')
        ) {
            if (!$this->_helper->getConfigAwayMode('day')) {
                return $page;
            }
            $fromTime = str_replace(',', '', $this->_helper->getConfigAwayMode('from_time'));
            $toTime   = str_replace(',', '', $this->_helper->getConfigAwayMode('to_time'));
            $days     = explode(',', $this->_helper->getConfigAwayMode('day'));
            $today    = date('w');
            $nowTime  = $this->_helper->convertToLocaleTime(date('His'), 'His');
            $checkDay = in_array($today, $days, true);

            if (($fromTime <= $nowTime && $nowTime <= $toTime) && $checkDay) {
                return $this->errorReport();
            }
        }

        return $page;
    }

    /**
     * @return null
     */
    protected function errorReport()
    {
        return $this->errorHelper->processSecurityReport(self::ERROR_CODE, __('You have been blocked from away mode.'));
    }
}

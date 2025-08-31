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

namespace Mageplaza\SecurityPro\Observer;

use Exception;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Mageplaza\Security\Helper\Data;
use Mageplaza\SecurityPro\Model\ActionLogFactory;

/**
 * Class LoginSuccess
 * @package Mageplaza\SecurityPro\Observer
 */
class LoginSuccess implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var ActionLogFactory
     */
    protected $_actionLogFactory;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Request
     */
    protected $_requestEnvironment;

    /**
     * LoginSuccess constructor.
     *
     * @param ActionLogFactory $actionLogFactory
     * @param Data $helperData
     * @param RequestInterface $request
     * @param Request $requestEnvironment
     */
    public function __construct(
        ActionLogFactory $actionLogFactory,
        Data $helperData,
        RequestInterface $request,
        Request $requestEnvironment
    ) {
        $this->_actionLogFactory   = $actionLogFactory;
        $this->_helperData         = $helperData;
        $this->_request            = $request;
        $this->_requestEnvironment = $requestEnvironment;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->_helperData->isEnabled()) {
            $userName = $observer->getUser()->getUserName();
            $this->_actionLogFactory->create()->addData([
                'time'             => time(),
                'user_name'        => $userName,
                'ip'               => $this->_requestEnvironment->getClientIp(),
                'action'           => 'login',
                'module'           => $this->_request->getControllerModule(),
                'status'           => 1,
                'full_action_name' => '	adminhtml_auth_login',
                'description'      => null
            ])->save();
        }
    }
}

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
use Magento\Backend\App\AbstractAction;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Mageplaza\SecurityPro\Helper\Data;
use Mageplaza\SecurityPro\Model\ActionLogFactory;

/**
 * Class Action
 * @package Mageplaza\SecurityPro\Plugin
 */
class Action
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * @var Request
     */
    protected $_requestEnvironment;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Session
     */
    protected $_authSession;

    /**
     * @var ActionLogFactory
     */
    protected $_actionLogFactory;

    /**
     * Action constructor.
     *
     * @param RequestInterface $request
     * @param Session $authSession
     * @param ActionLogFactory $actionLogFactory
     * @param Data $helper
     * @param Request $requestEnvironment
     */
    public function __construct(
        RequestInterface $request,
        Session $authSession,
        ActionLogFactory $actionLogFactory,
        Data $helper,
        Request $requestEnvironment
    ) {
        $this->_helper             = $helper;
        $this->_requestEnvironment = $requestEnvironment;
        $this->_request            = $request;
        $this->_authSession        = $authSession;
        $this->_actionLogFactory   = $actionLogFactory;
    }

    /**
     * @param AbstractAction $action
     *
     * @throws Exception
     */
    public function beforeExecute(AbstractAction $action)
    {
        $fullActionName = $this->_request->getFullActionname();
        if ($this->_helper->isEnabled()
            && $this->_request->getActionName() !== 'validate'
            && strpos($fullActionName, 'mui_') === false
            && !in_array($fullActionName, $this->_helper->getSkipActions(), true)
        ) {
            $this->_actionLogFactory->create()->addData([
                'time'             => time(),
                'user_name'        => $this->_authSession->isLoggedIn()
                    ? $this->_authSession->getUser()->getUserName()
                    : null,
                'ip'               => $this->_requestEnvironment->getClientIp(),
                'action'           => $this->_request->getActionName(),
                'module'           => $this->_request->getControllerModule(),
                'status'           => 1,
                'full_action_name' => $this->_request->getFullActionName(),
                'description'      => !empty($this->_request->getParams())
                    ? json_encode($this->_request->getParams())
                    : null

            ])->save();
        }
    }
}

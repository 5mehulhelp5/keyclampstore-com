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

namespace Mageplaza\SecurityPro\Block\Adminhtml\ActionLog\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\Security\Helper\Data;

/**
 * Class Form
 * @package Mageplaza\SecurityPro\Block\Adminhtml\ActionLog\Edit
 */
class Form extends Generic
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var DataForm $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id'      => 'view_form',
                'action'  => $this->getData('action'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            ]
        ]);
        $log  = $this->_coreRegistry->registry('mageplaza_security_actionlog');

        /** @var DataForm $form */
        $form->setHtmlIdPrefix('log_');
        $form->setFieldNameSuffix('log');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Action information'),
            'class'  => 'fieldset-wide'
        ]);
        $fieldset->addField('id', 'label', [
            'name'  => 'id',
            'label' => __('ID'),
        ]);
        $fieldset->addField('times', 'label', [
            'name'  => 'time',
            'label' => __('Time'),
            'title' => __('Time'),
            'value' => $this->_helper->convertToLocaleTime($log->getTime())
        ]);
        $fieldset->addField('user_name', 'label', [
            'name'  => 'user_name',
            'label' => __('User Name'),
            'title' => __('User Name'),
        ]);
        $fieldset->addField('ip', 'label', [
            'name'  => 'ip',
            'label' => __('IP'),
            'title' => __('IP'),
        ]);
        $fieldset->addField('action', 'label', [
            'name'  => 'action',
            'label' => __('Action'),
            'title' => __('Action'),
        ]);
        $fieldset->addField('module', 'label', [
            'name'  => 'module',
            'label' => __('Module'),
            'title' => __('Module'),
        ]);
        $fieldset->addField('stt', 'label', [
            'label' => __('Status'),
            'title' => __('Status'),
            'value' => $log->getStatus() ? __('Success') : __('Failed')
        ]);
        $fieldset->addField('full_action_name', 'label', [
            'name'  => 'full_action_name',
            'label' => __('Full Action Name'),
            'title' => __('Full Action Name'),
        ]);
        if ($log->getDescription()) {
            $fieldset->addField('description', 'label', [
                'name'  => 'mp_description',
                'label' => __('Description'),
                'title' => __('Description'),
            ]);
        }
        $form->addValues($log->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

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

namespace Mageplaza\SecurityPro\Block\Adminhtml\FileChange\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as FormData;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\Security\Helper\Data;

/**
 * Class Form
 * @package Mageplaza\SecurityPro\Block\Adminhtml\FileChange\Edit
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
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var FormData $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id'      => 'view_form',
                'action'  => $this->getData('action'),
                'method'  => 'post',
                'enctype' => 'multipart/form-data'
            ]
        ]);
        $log  = $this->_coreRegistry->registry('mageplaza_security_filechange');

        /** @var FormData $form */
        $form->setHtmlIdPrefix('log_');
        $form->setFieldNameSuffix('log');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('File Change information'),
            'class'  => 'fieldset-wide'
        ]);
        $fieldset->addField('id', 'label', [
            'name'  => 'id',
            'label' => __('ID'),
        ]);
        $fieldset->addField('file_name', 'label', [
            'name'  => 'file_name',
            'label' => __('File Name'),
            'title' => __('File Name'),
        ]);
        $fieldset->addField('path', 'label', [
            'name'  => 'path',
            'label' => __('Path'),
            'title' => __('Path'),
        ]);
        $fieldset->addField('old_hash', 'label', [
            'name'  => 'old_hash',
            'label' => __('Old Hash'),
            'title' => __('Old Hash')
        ]);
        $fieldset->addField('new_hash', 'label', [
            'name'  => 'new_hash',
            'label' => __('New Hash'),
            'title' => __('New Hash'),
        ]);
        $fieldset->addField('type', 'label', [
            'name'  => 'type',
            'label' => __('Type'),
            'title' => __('Type'),
        ]);
        $fieldset->addField('times', 'label', [
            'name'  => 'time',
            'label' => __('Time'),
            'title' => __('Time'),
            'value' => $log->getTime()
        ]);

        $form->addValues($log->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

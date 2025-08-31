<?php
namespace Best4Mage\DPPC\Block\Adminhtml\Side;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize dppc side edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'side_id';
        $this->_blockGroup = 'Best4Mage_DPPC';
        $this->_controller = 'adminhtml_side';

        parent::_construct();

        if ($this->_isAllowedAction('Best4Mage_DPPC::save')) {
            $this->buttonList->update('save', 'label', __('Save DPPC Side'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Best4Mage_DPPC::side_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Side'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded side
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('dppc_side')->getId()) {
            return __("Edit Side '%1'", $this->escapeHtml($this->_coreRegistry->registry('dppc_side')->getTitle()));
        } else {
            return __('New Side');
        }
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('dppc/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }
}

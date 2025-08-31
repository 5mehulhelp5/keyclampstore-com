<?php
namespace Best4Mage\DPPC\Block\Adminhtml\Shape\Edit;

/**
 * Adminhtml dppc shape edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Best4Mage\DPPC\Model\Shape\Source\Sides
     */
    protected $_dppcSides;

    /**
     * @var \Best4Mage\DPPC\Model\Shape\Source\Shapecodes
     */
    protected $_dppcShapeCodes;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Best4Mage\DPPC\Model\Shape\Source\Sides $dppcSides,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_dppcSides = $dppcSides;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('shape_form');
        $this->setTitle(__('Shape Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Best4Mage\DPPC\Model\Shape $model */
        $model = $this->_coreRegistry->registry('dppc_shape');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'),'enctype' => 'multipart/form-data', 'method' => 'post']]
        );
        $isElementDisabled = false;
        $form->setHtmlIdPrefix('shape_');

        if ($model->getData('side_id')) {
            $sideIds = explode(',', $model->getData('side_id'));
            $model->setData('side_id', array_values($sideIds));
        }

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getShapeId()) {
            $fieldset->addField('shape_id', 'hidden', ['name' => 'shape_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Shape Title'), 'title' => __('Shape Title'), 'required' => true]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => true,
                'options' => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );

        $sidesArray = $this->_dppcSides->getAllOptions();

        $fieldset->addField(
            'side_id',
            'multiselect',
            [
                'name' => 'side_id[]',
                'label' => __('Shape Sides'),
                'title' => __('Shape Sides'),
                'class' => 'shape-sides',
                'values' => $sidesArray
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Shape Image'),
                'title' => __('Shape Image')
            ]
        );

        $fieldset->addField(
            'formula',
            'textarea',
            ['name' => 'formula', 'label' => __('Shape Formula'), 'title' => __('Shape Formula'), 'required' => true]
        );

        $fieldset->addField(
            'calculation_type',
            'select',
            [
                'label' => __('Calculation Type'),
                'title' => __('Calculation Type'),
                'name' => 'calculation_type',
                'required' => true,
                'options' => ['1' => __('Surface'), '0' => __('Volume')]
            ]
        );

        $minMaxNote = '<p class="min_max_note">Enter comma separated {min}:{max} value pair for each sides in order. Example, 100:1000,100:5000<br/><strong>Note:</strong> These values should be based on your "Input Unit" that you select at DPPC product level settings.</p>';

        $fieldset->addField(
            'min_max_value',
            'textarea',
            ['name' => 'min_max_value', 'label' => __('Min Max Value (Input Unit)'), 'title' => __('Min Max Value'), 'after_element_html' => $minMaxNote]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            ['name' => 'sort_order', 'label' => __('Sort Order'), 'title' => __('Sort Order'), 'required' => false]
        );

        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '0' : '1');
        }

        if ($model->getData('image')) {
            $model->setData('image', $this->getViewFileUrl('Best4Mage_DPPC::images/'.basename($model->getData('image'))));
        }


        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilderFree
 * @copyright Copyright (C) 2023 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilderFree\Block;

class Builder extends \Magezon\Builder\Block\Builder
{
	/**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'Magezon_PageBuilderFree::builder.phtml';

	/**
	 * @var \Magezon\PageBuilderFree\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context        
	 * @param \Magezon\PageBuilderFree\Model\CompositeConfigProvider $configProvider
	 * @param \Magezon\PageBuilderFree\Helper\Data $dataHelper
	 * @param array $data
	 */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magezon\PageBuilderFree\Model\CompositeConfigProvider $configProvider,
        \Magezon\PageBuilderFree\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $configProvider, $data);
		$this->dataHelper = $dataHelper;
    }
}
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

namespace Magezon\PageBuilderFree\Model;

use Magento\Framework\App\ObjectManager;

class DefaultConfigProvider extends \Magezon\Builder\Model\DefaultConfigProvider
{
	/**
	 * @return array
	 */
	public function getConfig()
	{
		$config = parent::getConfig();
		$helper = ObjectManager::getInstance()->get(\Magezon\PageBuilderFree\Helper\Data::class);
		$config['profile'] = [
			'builder'     => \Magezon\PageBuilderFree\Block\Builder::class,
			'key'         => $helper->getKey(),
			'home'        => 'https://www.magezon.com/magento-2-page-builder-free'
		];
		$config['elementsModalPrefix'] = '<div class="mgz-builder-box-message">Get more with Magezon Page Builder Pro <a href="https://www.magezon.com/magezon-page-builder-for-magento-2.html?utm_source=free&utm_campaign=pagebuilder&utm_medium=gopro" class="mgz-btn mgz-builder-action-btn" target="_blank">GO PRO</a></div>';
		return $config;
	}
}
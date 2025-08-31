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

namespace Mageplaza\SecurityPro\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Security\Helper\Data as AbstractData;

/**
 * Class Data
 * @package Mageplaza\SecurityPro\Helper
 */
class Data extends AbstractData
{
    const XML_PATH_ACTION_LOG_BACKUP = 'action_log_backup';
    const XML_PATH_AWAY_MODE         = 'away_mode';
    const XML_PATH_FILE_CHANGE       = 'file_change';

    /**
     * Data constructor.
     *
     * @param TimezoneInterface $timezone
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TimezoneInterface $timezone,
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $objectManager, $storeManager, $timezone);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigBackUp($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(
            self::CONFIG_MODULE_PATH . '/' . self::XML_PATH_ACTION_LOG_BACKUP . $code,
            $storeId
        );
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigAwayMode($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . self::XML_PATH_AWAY_MODE . $code, $storeId);
    }

    /**
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigFileChange($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . self::XML_PATH_FILE_CHANGE . $code, $storeId);
    }

    /**
     * @return array
     */
    public function getSkipActions()
    {
        $skipAction = $this->getModuleConfig('skip_actions');

        return array_filter(array_map('trim', explode("\n", $skipAction)));
    }
}

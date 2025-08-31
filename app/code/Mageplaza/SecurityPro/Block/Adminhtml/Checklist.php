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

namespace Mageplaza\SecurityPro\Block\Adminhtml;

use Mageplaza\Security\Block\Adminhtml\Checklist as AbstractChecklist;

/**
 * Class Checklist
 * @package Mageplaza\SecurityPro\Block\Adminhtml
 */
class Checklist extends AbstractChecklist
{
    /**
     * @return bool
     */
    public function hasProPackage()
    {
        return true;
    }

    /**
     * @param $unSecureName
     *
     * @return string
     */
    public function getUserNameFixitUrl($unSecureName)
    {
        return $this->getUrl('adminhtml/user/edit', ['user_id' => $unSecureName['user_id']]);
    }

    /**
     * @return string
     */
    public function getFrontendCaptchaFixitUrl()
    {
        return $this->getUrl('mpsecurity/checklist/fixit', ['id' => 'frontend_captcha']);
    }

    /**
     * @return string
     */
    public function getBackendCaptchaFixitUrl()
    {
        return $this->getUrl('mpsecurity/checklist/fixit', ['id' => 'backend_captcha']);
    }

    /**
     * @return string
     */
    public function getVersionFixitUrl()
    {
        return 'https://www.mageplaza.com/devdocs/upgrade-magento-2.html';
    }

    /**
     * @return string
     */
    public function getDbFixitAdditionData()
    {
        $html = '<form id="prefix-form" action="' . $this->getUrl(
            'mpsecurity/checklist/fixit',
            ['id' => 'db_prefix']
        ) . '">';
        $html .= '<div class="db-notice"><p>';
        $html .= __('You should back up your database before adding a database prefix.');
        $html .= '<br>';
        $html .= __('And you need permission to write file: /app/etc/env.php');
        $html .= '</p></div>';
        $html .= __('New prefix') . ' <input type="text" name="prefix" maxlength="5">';
        $html .= '<input type="submit" value="Add">';
        $html .= '</form>';

        return $html;
    }

    /**
     * @return string
     */
    public function getAdditionalJavascript()
    {
        $html = 'require([
                    "jquery"
                ], function ($) {
                    $("#db-prefix").click(function () {
                        $("#prefix-form").toggle();
                    });
                    $("body").click(function (e) {
                        if (!$(e.target).closest("#prefix-form").length && e.target.id !== "prefix-form" && e.target.id !== "db-prefix") {
                            $("#prefix-form").hide();
                        }
                    });
                });';

        return $html;
    }
}

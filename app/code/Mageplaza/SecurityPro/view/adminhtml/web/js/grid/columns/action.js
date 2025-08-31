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

define([
    'Magento_Ui/js/grid/columns/column',
    'mage/translate'
], function (Column, $t) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },
        getLabel: function (record) {
            var label = this._super(record);

            if (label.includes('pdf')) {
                label = label.split('pdf');
                return $t('Print ') + label[1];
            }
            switch (record.full_action_name) {
                case 'adminhtml_system_config_edit':
                    label = $t('Config Edit');
                    break;
                case 'adminhtml_system_config_save':
                    label = $t('Config Save');
                    break;
                case 'adminhtml_cache_flushSystem':
                    label = $t('Flush Cache');
                    break;
            }

            return label;
        }
    });
});
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var globalOptions = {
        productId: null,
        priceHolderSelector: '.price-box', //data-role="priceBox"
        optionsSelector: '.product-custom-option',
        optionConfig: {},
        optionHandlers: {},
        optionTemplate: '<%= data.label %>' +
        '<% if (data.finalPrice.value > 0) { %>' +
        ' +<%- data.finalPrice.formatted %>' +
        '<% } else if (data.finalPrice.value < 0) { %>' +
        ' <%- data.finalPrice.formatted %>' +
        '<% } %>',
        controlContainer: 'dd'
    };

    /**
     * Custom option preprocessor
     * @param  {jQuery} element
     * @param  {Object} optionsConfig - part of config
     * @return {Object}
     */
    function defaultGetOptionValue(element, optionsConfig) {
        var changes = {},
            optionValue = element.val(),
            optionId = utils.findOptionId(element[0]),
            optionName = element.prop('name'),
            optionType = element.prop('type'),
            optionConfig = optionsConfig[optionId],
            optionHash = optionName;

        switch (optionType) {
            case 'text':
            case 'textarea':
                changes[optionHash] = optionValue ? optionConfig.prices : {};
                break;

            case 'radio':
                if (element.is(':checked')) {
                    changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                }
                break;

            case 'select-one':
                changes[optionHash] = optionConfig[optionValue] && optionConfig[optionValue].prices || {};
                break;

            case 'select-multiple':
                _.each(optionConfig, function (row, optionValueCode) {
                    optionHash = optionName + '##' + optionValueCode;
                    changes[optionHash] = _.contains(optionValue, optionValueCode) ? row.prices : {};
                });
                break;

            case 'checkbox':
                optionHash = optionName + '##' + optionValue;
                changes[optionHash] = element.is(':checked') ? optionConfig[optionValue].prices : {};
                break;

            case 'file':
                // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                changes[optionHash] = optionValue || element.prop('disabled') ? optionConfig.prices : {};
                break;
        }

        return changes;
    }

    $.widget('mage.priceOptions', {
        options: globalOptions,

        /**
         * @private
         */
        _init: function initPriceBundle() {
            $(this.options.optionsSelector, this.element).trigger('change');
        },

        /**
         * Widget creating method.
         * Triggered once.
         * @private
         */
        _create: function createPriceOptions() {
            
            var form = this.element,
                options = $(this.options.optionsSelector, form),
                priceBox = $(this.options.priceHolderSelector, $(this.options.optionsSelector).element);

            if (priceBox.data('magePriceBox') &&
                priceBox.priceBox('option') &&
                priceBox.priceBox('option').priceConfig
            ) {
                if (priceBox.priceBox('option').priceConfig.optionTemplate) {
                    this._setOption('optionTemplate', priceBox.priceBox('option').priceConfig.optionTemplate);
                }
                this._setOption('priceFormat', priceBox.priceBox('option').priceConfig.priceFormat);
            }

            this._applyOptionNodeFix(options);

            options.on('change', this._onOptionChanged.bind(this));
            options.on('keyup', this._onOptionChanged.bind(this));
            options.on('selected', this._onOptionChanged.bind(this));
        },

        /**
         * Custom option change-event handler
         * @param {Event} event
         * @private
         */
        _onOptionChanged: function onOptionChanged(event) {
            
            var $widget = this;
            var optionConfig = this.options.optionConfig;
            var changes,$price = null, 
                option = $(event.target),
                optionType =option.prop('type'),
                handler = this.options.optionHandlers[option.data('role')];

            if(Object.keys(optionConfig).length > 0){
                $.each(optionConfig, function(index, el) {
                    var nwChild = 1;
                    
                    $.each(el, function(childIndex, childEl) {
                        ++nwChild;
                        var RadioCheckboxLabel = 'options_'+index+'_'+nwChild;
                        var TextLabel =  'options_'+index+'_'+optionType;
                        var Select = 'select_'+index;
                        
                        if(childEl.type == "percent" || el.type == "percent" ){
                            if(optionType == "text" || optionType == "textarea" || optionType == "file" || (typeof el.prices != 'undefined') ){
                                if(typeof el.prices != 'undefined'){
                                    $price = el.prices.finalPrice.percent;
                                    //chnage text ,textarea , file type label
                                    $("label[for='"+TextLabel+"'] .price-wrapper").text($price + "%");
                                    el.prices = $widget._getPrice($price);
                                    
                                    //change time ,date ,and date&time type label
                                    var $dateTime = $("#options_"+index+"_month").parent().siblings().find(".price-notice .price-wrapper").html();
                                    var $hour = $("#options_"+index+"_hour").parent().siblings().find(".price-notice .price-wrapper").html();
                                    if((typeof $dateTime != "undefined") || (typeof $hour != "undefined")){
                                        $("#options_"+index+"_month").parent().siblings().find(".price-notice .price-wrapper").text($price + "%");
                                        $("#options_"+index+"_hour").parent().siblings().find(".price-notice .price-wrapper").text($price + "%");
                                    }
                                }
                            }else{
                                if(typeof childEl.prices != 'undefined'){
                                    $price = childEl.prices.finalPrice.percent ;   
                                    $("label[for='"+RadioCheckboxLabel+"'] .price-wrapper").text($price + "%");
                                    //change checkbox ,radio , drop-down and select-multiple type label
                                    var $data = $("option[value='"+childIndex+"']").text();
                                    var str = $data.substring($data.indexOf("+") + 1);
                                    var textReplace = $data.replace(str,$price+"%");
                                    $("option[value='"+childIndex+"']").text(textReplace);
                                    childEl.prices = $widget._getPrice($price);
                                }
                            }
                        }
                    });
                });
            }

            if (handler && handler instanceof Function) {
                changes = handler(option, this.options.optionConfig, this);
            } else {
                changes = defaultGetOptionValue(option, this.options.optionConfig);
                
            }
            
            $(this.options.priceHolderSelector).trigger('updatePrice', changes);
        },

        /**
         * Helper to fix issue with option nodes:
         *  - you can't place any html in option ->
         *    so you can't style it via CSS
         * @param {jQuery} options
         * @private
         */
        _applyOptionNodeFix: function applyOptionNodeFix(options) {
            var config = this.options,
                format = config.priceFormat,
                template = config.optionTemplate;

            template = mageTemplate(template);
            options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId];

                $element.find('option').each(function (idx, option) {
                    var $option,
                        optionValue,
                        toTemplate,
                        prices;

                    $option = $(option);
                    optionValue = $option.val();

                    if (!optionValue && optionValue !== 0) {
                        return;
                    }

                    toTemplate = {
                        data: {
                            label: optionConfig[optionValue] && optionConfig[optionValue].name
                        }
                    };
                    prices = optionConfig[optionValue] ? optionConfig[optionValue].prices : null;

                    if (prices) {
                        _.each(prices, function (price, type) {
                            var value = +price.amount;

                            value += _.reduce(price.adjustments, function (sum, x) { //eslint-disable-line
                                return sum + x;
                            }, 0);
                            toTemplate.data[type] = {
                                value: value,
                                formatted: utils.formatPrice(value, format)
                            };
                        });

                        $option.text(template(toTemplate));
                    }
                });
            });
        },

        _getPrice: function (percent) {
            
            var priceBox,newPrice,formatPrice,
            formatPrice = $("#dppc_price").val();
            newPrice = (formatPrice*percent)/100;
            return {
                basePrice: {amount: newPrice, percent:percent , adjustments: {} },
                finalPrice: {amount: newPrice, percent:percent  , adjustments: {} },
                oldPrice: {amount: newPrice, percent:percent , adjustments: {} }
            };
        },

        /**
         * Custom behavior on getting options:
         * now widget able to deep merge accepted configuration with instance options.
         * @param  {Object}  options
         * @return {$.Widget}
         * @private
         */
        _setOptions: function setOptions(options) {
            $.extend(true, this.options, options);
            this._super(options);

            return this;
        }
    });

    return $.mage.priceOptions;
});

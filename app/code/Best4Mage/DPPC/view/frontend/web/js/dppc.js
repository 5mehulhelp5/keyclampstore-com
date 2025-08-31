/**
 * Best4Mage - Dynamic Product Price Calculator
 * @author Best4Mage
 */

define([
	"jquery",
	"mage/translate",
	"mage/template", 
	"priceUtils", "math", 
	"mage/validation",
	"Magento_Swatches/js/swatch-renderer"
], function ($, $t, mageTemplate, utils, math) {

    "use strict";
    $.widget('dppc.calculation', {
        options: {
            config: {},
            shapeContainerSelector: '.shape-options-wrapper',
            radioSelector: '.shape-radio',
            inputSelector: '.side .input-text',
            buttonSelector: '#product-addtocart-button',
            priceHolderSelector: '.price-box',
            swatchOption: '.swatch-input'
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            // Bind Events
            this._onLoadRemoveRadioClick();
            this._bindEvents();
            this._addValidators();
            this._showContainer();
            this.finalPrice = null;
            this._setPreselectedDPPCOptions();            
        },

        _addValidators: function () {

        	var $widget = this;

        	$.validator.addMethod('validate-area', $widget.validateArea.bind($widget), function () { return $widget.areaErrorMessage; });

        	$.validator.addMethod('validate-area-price', $widget.validateAreaPrice.bind($widget), function () { return $widget.areaPriceErrorMessage; });

        	$.validator.addMethod('validate-min-max', $widget.validateMinMax.bind($widget), function () { return $widget.minMaxErrorMessage; });

        	$.validator.addMethod("validate-shape", $widget.validateShape.bind($widget),function () { return $widget.shapeErrorMessage; });
      
            
            var elem = $('.sides .input-text');
                 	   
            if(elem.length) {
                elem.addClass('validate-min-max');
                
                elem.on('keyup', $widget, $widget.reloadOnkeyUp);
            }

            /*$($widget.options.buttonSelector).on('click', function(event) {
            	var ele = $('#minimum-area-price');
            	$.validator.validateElement(ele);
            });*/
	    },

	    _setPreselectedDPPCOptions: function (){
	    	var $widget = this,
        		options = $widget.options,
        		preSelectedDPPCValues = $widget.options.config.preSelectedDPPCValues;

        	if(preSelectedDPPCValues != null){
        		var selectedShape = preSelectedDPPCValues['selected_shape'];
        		var sideOptions = preSelectedDPPCValues['side_options'];
        		$('.shape-radio[data-id='+selectedShape+']').click();

        		$.each(sideOptions, function(index, el) {
        			$('.sides input[id ='+selectedShape+'_'+index+']').val(el);
        			//$('.sides input[data-side-id ='+index+']').trigger('keyup');

        			$widget._addCustomPrices($('.shape-radio[data-id="' + selectedShape + '"]'));
        		});
        	}

	    },

	    _showContainer: function () {
	    	
	    	var $widget = this,
	    		options = $widget.options,
        		preSelectedDPPCValues = $widget.options.config.preSelectedDPPCValues;
        	
	    	if($widget.options.config.productType == 'configurable' && preSelectedDPPCValues == null){
    			$($widget.options.shapeContainerSelector).hide();
    		}else{
    			
    			$($widget.options.shapeContainerSelector).show();
    			$widget._setPreselectedDPPCOptions();
    		}	    	
	    },

	    reloadOnkeyUp:function (e) { 
	        window.clearTimeout(this.keyUpTimer); 
	        this.keyUpTimer = window.setTimeout(
	            function () { 
	            $.validator.validateElement(e.target);
	            }, 400
	        );  
	    },

        _bindEvents: function () {
        	var $widget = this,
        		options = $widget.options,
        		optionsPrice = JSON.parse($widget.options.config.optionsPrice);
        		
        	$(options.radioSelector).on('change', $widget, $widget._showSides);
        	$(options.inputSelector).on('keyup', $widget, $widget._reloadPrices);
        	$(options.swatchOption).on('change', $widget, $widget._swatchOnClick);
        	

        	if($('#product_addtocart_form').data('mageConfigurable') != undefined){
        		var dropDwonElement = $('#product_addtocart_form').data('mageConfigurable').options.settings;

	        	$(dropDwonElement).each(function(index, el) {
	        		$(el).on('change', $widget, $widget._optionOnSelect);
	        	});
        	}
        	

        	if($('.shape-option-list li').length == 1){
        		$(options.radioSelector).click();
        	}

        },

        _onLoadRemoveRadioClick: function () {
        	$(".shape-radio").prop("checked", false);	
        	$('.side .input-text').val('');
        },
        
        _showSides: function (e) {
        	var $widget = e.data;
        	var elementId = $(this).attr('id');
        	var shapeId = $(this).attr('data-id');
        	$('.sides').hide();        		
        	$('#selected_shape').val(shapeId);

        	var shapeElement = $('#selected_shape');
        	$.validator.validateElement(shapeElement);

        	$('.'+elementId).show();
        	$widget._addUnitPrice();
        	$widget._addCustomPrices($(this));

        },

        _swatchOnClick: function (e) {

        	var $widget = e.data;
        	var options = $widget.options;

        	$(options.inputSelector).val('');

            options.config.currentProduct = $('[data-role=swatch-options]').data('mageSwatchRenderer').getProduct();

            $widget._showShapeContainer();
        },

        _showShapeContainer: function () {
        	var $widget = this;
        	var options = $widget.options;

        	var showShapeContainer = true;
        	
        	$('#option-area-value').empty();
        	$('.shape-radio').prop('checked', false);
        	$('#selected_shape').val('');
        	$('.sides').hide();

        	if($('.shape-option-list li').length == 1){
        		$(options.radioSelector).click();
        	}
        	

        	$('.super-attribute-select').each(function(index, el) {
                if($(el).val() == ''){              
                    showShapeContainer = false;
                }
            });

            if(showShapeContainer){
                $('.shape-options-wrapper').show();
            }else{
                $('.shape-options-wrapper').hide();
            }

            $widget._addUnitPrice();
        },

        _optionOnSelect: function (e) {

        	var $widget = e.data;
        	var options = $widget.options;

        	$(options.inputSelector).val('');

        	var simpleProductId = $('#product_addtocart_form').data('mageConfigurable').simpleProduct;

            if(simpleProductId != undefined && simpleProductId != ''){
            	options.config.currentProduct = simpleProductId;
            }

            $widget._showShapeContainer();
        },

        _reloadPrices: function (e) {
        	var $widget = e.data;
        	var dataId = $(this).closest('li.sides').attr('data-id');
        	$widget._addCustomPrices($('.shape-radio[data-id="' + dataId + '"]'));
        },
        
        _addUnitPrice: function () {
        	var $widget = this;
	        var measurement = $widget._getMeasurement();
	        var template = mageTemplate('#measurement-price-template');
	        var elem = $("#measurement-price-info");
	        var optionsPrice = JSON.parse($widget.options.config.optionsPrice);
	        
	        if(!elem) { 
	            elem = document.createElement('div');
	            elem.setAttribute('id', 'measurement-price-info');
	            elem.setAttribute('class', 'measurement-price-info');
	            $('#shape-wrapper') && $('#shape-wrapper').before(elem);
	        }

	        var selectedShape = $('input.shape-radio:checked').attr('areatype');
	        var finalOutputUnit = measurement.outputSqUnitName;

	        var selectedShapeId = $('input.shape-radio:checked').attr('data-id');
	        var shapeCalType = $widget.options.config.calculationType[selectedShapeId];

	        if(shapeCalType != undefined && shapeCalType != ''){
	        	// shapeCalType : 0 = Volume ; 1 = Surface
	        	if(shapeCalType == 0){
	        		finalOutputUnit = measurement.outputVolUnitName;
	        	}else{
	        		finalOutputUnit = measurement.outputSqUnitName;
	        	}
	        }

	        if(selectedShape != undefined && selectedShape != ''){
	        	if(selectedShape.toLowerCase() == 'length'){
	        		finalOutputUnit = measurement.outputUnit.toLowerCase();
	        	}
	        }
	        var newField = template({
	            data: {
	                basePrice: utils.formatPrice(measurement.basePrice,optionsPrice.priceFormat),
	                outputunit: finalOutputUnit,
	                unitPrice: utils.formatPrice(measurement.unitPrice,optionsPrice.priceFormat)
	            }
	        });

	        $(elem).html(newField);   
	    },

	    _addCustomPrices: function (element) {
	    	var $widget = this;
	    	var optionsPrice = JSON.parse($widget.options.config.optionsPrice);

	    	var form = this.element,
                options = $(this.options.optionsSelector, form);

	    	var shapeId = element.attr('data-id');
            var convertedArea = $widget._getArea(shapeId);

            var measurement = $widget._getMeasurement();

			var finalPriceInput = $widget.getFinalPriceElement(shapeId);

			var input = $widget.getInputElement(shapeId);
			
			$(input).attr('valueid', convertedArea);

		    $(input).val(convertedArea);

            var inputValue = convertedArea ? (Math.round(convertedArea * 100)/100) : 0;
            
            var totalUnitText = $t('Total Unit: %s %s </br>Input Unit: %s');

            totalUnitText = totalUnitText.replace('%s', inputValue);
            totalUnitText = totalUnitText.replace('%s', $t(measurement.outputUnit));
            totalUnitText = totalUnitText.replace('%s', $t(measurement.inputUnit)); 
            $('#option-area-value').html(totalUnitText);            
             
            var price  = (convertedArea * parseFloat(measurement.unitPrice)); 
            
            var dppc_hidden = price + measurement.basePrice;
            if($widget.options.config.productType == 'configurable' && !$widget.options.config.currentProduct) {
                //optionsPrice.addCustomPrices(element.id || optionId, {price:0});  return false;
            }

            $.validator.validateElement($(input));

            var changes = $widget._getPrice(price);

        	$($widget.options.priceHolderSelector).trigger('updatePrice', changes);
        	
        	$("#dppc_price").val(dppc_hidden);
        	
        	
        	$widget.getFinalPrice();	        
        	$widget._addUnitPrice();
        	$(".product-options-wrapper .control input.product-custom-option, .product-options-wrapper .control textarea.product-custom-option, .product-options-wrapper .control select.product-custom-option").each(function(index ,el){$(el).trigger('change')});
        	// $(".product-options-wrapper .control select").each(function(index ,el){$(el).trigger('change')});	

	    },

	    getFinalPriceElement: function (shapeId) {
	        var $widget = this;

	        var finalPriceInput = $('#minimum-area-price');
	                    
	        if(!finalPriceInput.length) {

	            finalPriceInput = '<input id="minimum-area-price" class="validate-area-price dppc-validate-input" type="text">';
	             
	            //var priceBox = $('#product-price-'+$widget.options.config.productId);

	            var priceBox = $('#measurement-price-info');
	            
	            //priceBox = priceBox && priceBox.closest('.price-box') ? priceBox.closest('.price-box') : priceBox;
	            
	            if($widget.options.config.productType == 'configurable') {
	                if(priceBox) {
	                   $(finalPriceInput).insertBefore(priceBox);
	                } 
	            } else { 
	                setTimeout(
	                    function () {
	                    
	                    if(priceBox) { 
	                        $(finalPriceInput).insertBefore(priceBox);
	                    } 
	                
	                    }.bind(this), 1000
	                );  
	            }
	        } 
	        
	        return finalPriceInput;
	    },	

	    getInputElement: function (shapeId) {
        
	        var input = $('#option-area-input');
	        
	        if(!input.length) {
	            input = '<input id="option-area-input" class="dppc-validate-input validate-area" name="option-area-input" optionid="'+shapeId+'" valueid="" type="text">';
				input += '<input type="hidden" id="dppc_price" name="dppc_price" value="">';
	            $('.shape-option-list').append(input);
	        }  
	                
	        return input;
	    },    

	    _getArea: function (shapeId) {

	        var $widget = this;
	        var formula = $widget.options.config.formula[shapeId];
	        var area = 0;
	        var hasValue = false;
	        var sides = $widget.options.config.sides[shapeId];
	        let scope = {M_PI: Math.PI, PI: Math.PI};
	        $('#selected_shape_formula').val(formula);


	        $.each(sides, function(index, el) {
	        	var element = $('#'+shapeId+'_'+index);
	        	var eleVal = $('#'+shapeId+'_'+index).val();
	        	//if(eleVal.length){
	        		//hasValue = true;
	        		var eleConvertedVal = $widget._convertArea((element && eleVal) ? parseFloat(eleVal) : 0);
	        		scope[index] = eleConvertedVal;
	        	//}
	        	
	        });

	        //if(hasValue){
	        	area = math.eval(formula, scope);
	        //}
	        return area;
	    },

	    _getMeasurement: function (baseConfig) {         
 			var $widget = this,
        		measurement = $widget.options.config.measurement;
	        
	        if(baseConfig) {
	            return measurement;
	        }
	          
	        if($widget.options.config.currentProduct && measurement.configurable && measurement.configurable[$widget.options.config.currentProduct]) {
	            measurement = measurement.configurable[$widget.options.config.currentProduct];
	        }
	        
	        return measurement;
	    },

	    _convertArea: function (value) {
	        var $widget = this,
	        	measurement = $widget._getMeasurement();
	        
	        var unit = measurement.unit;
	        var inputUnit = unit[measurement.inputUnit];
	         
	        if(value && inputUnit[measurement.outputUnit]) {
	            var outputUnit = inputUnit[measurement.outputUnit];
	                 
	            return (parseFloat(outputUnit) * parseFloat(value));
	        }
	        
	        return value;
	    },

	    validateArea: function (v, e) {

	        var $widget = this,
	        	measurement = $widget._getMeasurement();
	        
	        var minUnit = parseFloat(measurement.minUnit);
	        
	        var maxUnit = parseFloat(measurement.maxUnit);
	        
	        var outputUnit =  measurement.outputUnit;
	        
	        var value = parseFloat(v);
	        
	        var result = false;
	         
	        var errorMsg = '';

	        if((minUnit > 0) && (maxUnit > 0)) {
	                if(minUnit > maxUnit) {
	                    if(value <= maxUnit) {
	                        result = true;
	                    } else {
	                    	result = false;
	                        errorMsg = $t('Maximum unit %s %s allowed.'); 
	                        errorMsg = errorMsg.replace('%s', maxUnit);
	                        errorMsg = errorMsg.replace('%s', $t(outputUnit));
	                    }
	                } else {
	                    if(value >= minUnit && value <= maxUnit) {
	                        result = true;
	                    } else {
	                    	result = false;
	                        errorMsg = $t('Your measurement must between %s and %s  %s.'); 
	                        errorMsg = errorMsg.replace('%s', minUnit);
	                        errorMsg = errorMsg.replace('%s', maxUnit);
	                        errorMsg = errorMsg.replace('%s', $t(outputUnit)); 
	                    }
	                } 
	        } else if((minUnit > 0) && (maxUnit <= 0)) {
	            if(value >= minUnit) {
	                result = true;
	            } else { 
	            	result = false;
	                    errorMsg = $t('Minimum unit %s %s required.'); 
	                    errorMsg = errorMsg.replace('%s', minUnit); 
	                    errorMsg = errorMsg.replace('%s', $t(outputUnit)); 
	            }
	        } else if((minUnit <= 0) && (maxUnit > 0)) {
	            if(value <= maxUnit) {
	                result = true;
	            } else {
	            	result = false;
	                errorMsg = $t('Maximum unit %s %s allowed.'); 
	                errorMsg = errorMsg.replace('%s', maxUnit);
	                errorMsg = errorMsg.replace('%s', $t(outputUnit));
	            }
	        } else if((minUnit <= 0) && (maxUnit <= 0)) {
	            if(value > 0) {	
	                result = true;
	            } else {
	            	result = false;
	                errorMsg = $t('This is a required field.');
	            }
	        } 

	        $widget.areaErrorMessage = errorMsg;

	        return result;
	    },
	    validateShape: function (v, e) {
	    	var $widget = this;

	        var result = false;
	         
	        var errorMsg = '';

	        var value = parseInt(v);

	        if(value){
	        	result = true;
	        }else{
	        	$widget.shapeErrorMessage = errorMsg = $t('Please select a shape!');
	        	result = false;
	        }

	        return result;
	    },
	    _getPrice: function (price) {
        
	        var $widget = this,
	        	measurement = $widget._getMeasurement();
	        
	        if(price <= 0) {
	            return {dppc_price: {
				        	basePrice: {amount: 0, adjustments: {} },
				        	finalPrice: {amount: 0, adjustments: {} },
				        	oldPrice: {amount: 0, adjustments: {} }
			        	}};
	        } 
	        
	        return {dppc_price: {
			        	basePrice: {amount: price, adjustments: {} },
			        	finalPrice: {amount: price, adjustments: {} },
			        	oldPrice: {amount: price, adjustments: {} }
			        }};
	    },
	    validateAreaPrice: function (v,e) {
       
	        var $widget = this,
	        	measurement = $widget._getMeasurement(),
	        	result = true,
	        	errorMsg = '';
	        
	        $(e).val($widget.finalPrice);

	        var value = $widget.finalPrice;

	        if(measurement.minimumPrice <= 0) {
	            result = true; 
	        } else if(measurement.minimumPrice && measurement.minimumPrice > value) {
	            result = false;
	        }

            errorMsg = $t('Minimum price %s are required. sadadads');
            errorMsg = errorMsg.replace('%s', $widget.options.config.currencySymbol+measurement.minimumPrice);

            $widget.areaPriceErrorMessage = errorMsg;

            if(!result) {
            	setTimeout(function(){
	            	var elmId = $(e).attr('id')+'-error';
	            	var advice = $('#'+elmId);
	            	$('html, body').animate({scrollTop: $(advice).offset().top-60}, 2000);
            	},100);
            }

	        return result;
	    },

	    getFinalPrice: function (){
	    	var $widget = this;
	    	var finalePriceElement = $($widget.options.priceHolderSelector).data('magePriceBox').cache.displayPrices.finalPrice.final;
	    	$widget.finalPrice = finalePriceElement;
	    },

	    validateMinMax:function (v,e) {
        	var $widget = this;
	        var optionId = '';   var value = parseInt(v);
	        optionId = $(e).closest('.sides').attr('data-id');

	        var sideId = $(e).attr('data-side-id');
	        var range = {min_value:0,max_value:0};
	        
	          
	        if($widget.options.config.minMax) {
	            if($widget.options.config.minMax && $widget.options.config.minMax[optionId] && $widget.options.config.minMax[optionId][sideId]) {
	                 range = $widget.options.config.minMax[optionId][sideId];
	            }
	        }
	        
	        range.min_value = range.min_value ? parseFloat(range.min_value) : 0;
	        
	        range.max_value = range.max_value ? parseFloat(range.max_value) : 0;
	        
	        var result = false;
	        
	        var errorMsg = '';	        
	        
	        if($.mage.isEmpty(value)) {
	            result = true; 
	        } else if(!$.isNumeric(value)) {
	            result = false; 
	            errorMsg = $t('Please enter a valid number in this field.');
	        } else if((range.min_value > 0) && (range.max_value > 0)) {
	            if(range.min_value > range.max_value) {
	                if(value <= range.max_value) {
	                    result = true; 
	                } else {
	                    errorMsg = $t('Maximum value %s allowed.');  
	                    errorMsg = errorMsg.replace('%s', range.max_value);
	                }  
	            } else {
	                if(value >= range.min_value && value <= range.max_value) {
	                    result = true;
	                } else {
	                    errorMsg = $t('Please enter value between %s and %s.');
	                    errorMsg = errorMsg.replace('%s', range.min_value);
	                    errorMsg = errorMsg.replace('%s', range.max_value);  
	                }
	            }  
	        } else if((range.min_value > 0) && (range.max_value <= 0)) {
	            if(value >= range.min_value) {
	                result = true; 
	            } else {
	                errorMsg = $t('Minimum value %s required.');
	                errorMsg = errorMsg.replace('%s', range.min_value);
	            } 
	        } else if((range.min_value <= 0) && (range.max_value > 0)) {
	            if(value <= range.max_value) {
	                result = true; 
	            } else {
	                errorMsg = $t('Maximum value %s allowed.');  
	                errorMsg = errorMsg.replace('%s', range.max_value);
	            }   
	        } else if((range.min_value <= 0) && (range.max_value <= 0)) {
	            if(value > 0) {
	                result = true; 
	            } else {
	                errorMsg = $t('This is a required field.');
	            }
	        }

	        $widget.minMaxErrorMessage = errorMsg;
	  
	        return result;
	    },

    });

    return $.dppc.calculation;
});

define(
    [
        "jquery",
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../model/shipping-rates-validator/paczkomaty2',
        '../model/shipping-rates-validation-rules/paczkomaty2',
        'Magento_Checkout/js/model/quote',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function (
        $,
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        shippingRatesValidator,
        shippingRatesValidationRules,
        quote,
        $t,
        shippingService
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('smpaczkomaty2', shippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('smpaczkomaty2', shippingRatesValidationRules);

        quote.shippingMethod.subscribe(function () {
            if($('#label_method_paczkomaty2_smpaczkomaty2').length && $('#label_method_paczkomaty2_smpaczkomaty2 a').length == 0) {

                var defaultLabel = $('#label_method_paczkomaty2_smpaczkomaty2').text();

                if($('#paczkomaty_point').val().length === 0) {

                    var addToLabel = '<br/><a href="#" id="select_paczkomaty_point">' + $t('Wybierz paczkomat') + '</a>';
                    var addToLabelCod = '<br/><a href="#" id="select_paczkomaty_point_cod">' + $t('Wybierz paczkomat') + '</a>';
                    $('#paczkomaty_point').val('');

                } else {

                    var addToLabel = '<br/><a href="#" id="select_paczkomaty_point">' + $t('Paczkomat') + ' ' + $('#paczkomaty_point').val() + '. ' + $t('Zmień paczkomat') + '</a>';
                    var addToLabelCod = '<br/><a href="#" id="select_paczkomaty_point_cod">' + $t('Paczkomat') + ' ' + $('#paczkomaty_point').val() + '. ' + $t('Zmień paczkomat') + '</a>';

                }

                $('#label_method_paczkomaty2_smpaczkomaty2').html(defaultLabel + addToLabel);
                $('#label_method_paczkomaty2cod_smpaczkomaty2').html(defaultLabel + addToLabelCod);
            }
        });

        shippingService.isLoading.subscribe(function (isLoading) {
            if (isLoading) {
                return
            }

            if($('#label_method_paczkomaty2_smpaczkomaty2').length && $('#label_method_paczkomaty2_smpaczkomaty2 a').length == 0) {

                var defaultLabel = $('#label_method_paczkomaty2_smpaczkomaty2').text();

                if($('#paczkomaty_point').val().length === 0) {

                    var addToLabel = '<br/><a href="#" id="select_paczkomaty_point">' + $t('Wybierz paczkomat') + '</a>';
                    var addToLabelCod = '<br/><a href="#" id="select_paczkomaty_point_cod">' + $t('Wybierz paczkomat') + '</a>';
                    $('#paczkomaty_point').val('');

                } else {

                    var addToLabel = '<br/><a href="#" id="select_paczkomaty_point">' + $t('Paczkomat') + ' ' + $('#paczkomaty_point').val() + '. ' + $t('Zmień paczkomat') + '</a>';
                    var addToLabelCod = '<br/><a href="#" id="select_paczkomaty_point_cod">' + $t('Paczkomat') + ' ' + $('#paczkomaty_point').val() + '. ' + $t('Zmień paczkomat') + '</a>';

                }

                $('#label_method_paczkomaty2_smpaczkomaty2').html(defaultLabel + addToLabel);
                $('#label_method_paczkomaty2cod_smpaczkomaty2').html(defaultLabel + addToLabelCod);
            }
        });


        return Component;
    }
);
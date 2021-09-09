define([
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/quote',
    'jquery'
], function (Abstract, validator, $t, shippingService, quote, $) {
    'use strict';

    return Abstract.extend({
        validate: function () {
            var value   = this.value(),
                result  = validator(this.validation, value, this.validationParams),
                message = result.message,
                isValid = result.passed;
            var shippingRates = shippingService.getShippingRates();
            var isPaczkomatyAvailable = false;
            shippingRates().forEach(function(rate,i) {
                if(rate.carrier_code == 'smpaczkomaty2') isPaczkomatyAvailable = true;
            });

            if(isPaczkomatyAvailable && quote.shippingMethod() && quote.shippingMethod().carrier_code == 'smpaczkomaty2') {
                this.error(message);
                this.bubble('error', message);

                var shippingAddress = quote.shippingAddress();
                if(!shippingAddress.telephone && !$('[name="telephone"]').val()) {
                    this.source.set('params.invalid', true);
                    alert($t('Proszę podać numer telefonu dla wysyłki Paczkomatami!'));
                    isValid = false;
                } else if (!$('#paczkomaty_point').val()) {
                    if($.data(document.body,'paczkomaty_point')) {
                        $('#paczkomaty_point').val(
                            $.data(document.body,'paczkomaty_point')
                        );
                    } else {
                        this.source.set('params.invalid', true);
                        alert($t('Proszę wybrać paczkomat'));
                        isValid = false;
                    }
                }
            } else {
                isValid = true;
            }

            return {
                valid: isValid,
                target: this
            };
        }
    });
});
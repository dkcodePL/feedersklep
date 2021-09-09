define(
    [
        'jquery',
        'mageUtils',
        '../shipping-rates-validation-rules/paczkomaty2',
        'mage/translate'
    ],
    function ($, utils, validationRules, $t) {
        'use strict';
        return {
            validationErrors: [],
            validate: function (address) {
                var self = this;
                this.validationErrors = [];

                $.each(validationRules.getRules(), function (field, rule) {
                    var message = '';
                    if (rule.required && field === 'telephone' && utils.isEmpty(address[field])) {
                        message = $t('Podaj numer telefonu dla wysyłki Paczkomatami!');
                        self.validationErrors.push(message);
                    }
                });
                return !Boolean(this.validationErrors.length);
            }
        };
    }
);
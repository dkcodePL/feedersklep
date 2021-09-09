/*
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/ui-select'
], function ($, _, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            modules: {
                type: '${ $.parentName }.type'
            },
            imports: {
                update: '${ $.parentName }.type:value'
            }
        },
        initialize: function () {
            this._super();
            var self = this;

            self.showField();
        },
        /**
         * @param {String} value
         */
        update: function (value) {

            this.showField(value);

        },
        showField: function (value = null) {

            var self = this,
            type = value ? value : self.type().value();

            switch (type) {
                case 'category':
                    self.visible(true);
                    break;
                default:
                    self.visible(false);
                    break;
            }

        },


    });
});

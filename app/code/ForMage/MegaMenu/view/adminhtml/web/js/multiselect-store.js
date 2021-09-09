/*
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function ($, _, registry, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            modules: {
                parent: '${ $.parentName }.parent'
            }
        },
        initialize: function () {
            this._super();

            if (this.parent().value() == 1) {
                this.visible(true);
            }
        }
    });
});

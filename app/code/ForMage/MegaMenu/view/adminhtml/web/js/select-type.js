/*
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, registry, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            modules: {
                cms: '${ $.parentName }.cms',
                block: '${ $.parentName }.block',
                desc: '${ $.parentName }.description',
                custom: '${ $.parentName }.custom_name',
                full: '${ $.parentName }.full_width',
                parent: '${ $.parentName }.parent',
                url: '${ $.parentName }.custom_url',
                class: '${ $.parentName }.class',
                drop: '${ $.parentName }.drop_down',
            }
        },
        initialize: function () {
            this._super();
            var self = this;

            if (self.parent().value() > 1) {

                setTimeout(function () {
                    self.setVisible(true);
                    self.showFields();
                }, 2000);


            }
        },
        onUpdate: function () {
            this._super();

            this.showFields();

        },

        showFields: function () {

            var self = this;

            switch (self.value()) {
                case 'category':
                    self.cms().visible(false);
                    self.block().visible(false);
                    self.desc().visible(false);
                    self.drop().visible(true);
                    break;
                case 'current-category':
                    self.cms().visible(false);
                    self.block().visible(false);
                    self.desc().visible(false);
                    self.drop().visible(true);
                    break;
                case 'cms':
                    self.cms().visible(true);
                    self.block().visible(false);
                    self.drop().visible(false);
                    break;
                case 'block':
                    self.block().visible(true);
                    self.cms().visible(false);
                    self.drop().visible(false);
                    break;
                case 'custom':
                    self.cms().visible(false);
                    self.block().visible(false);
                    self.desc().visible(true);
                    self.url().visible(true);
                    self.drop().visible(false);
                    break;
                default:
                    self.cms().visible(false);
                    self.block().visible(false);
                    self.desc().visible(false);
                    self.url().visible(false);
                    self.drop().visible(false);
                    break;
            }

            self.class().visible(true);
            self.full().visible(true);
            self.custom().visible(true);

        },

    });
});

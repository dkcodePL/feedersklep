define([
    'jquery',
    'matchMedia',
    'collapsible',
    'jquery-ui-modules/widget'
], function ($) {

    $.widget('formage.collapsibleBlock', {

        options: {
            collapsible: {
                collapsible: true,
                header: '.item.title',
                trigger: '',
                content: '.item.content',
                openedState: 'active',
                animate: false
            },
            mediaBreakpoint: '(max-width: 768px)'
        },

        /**
         * @private
         */
        _create: function () {
            var self = this;
            this._super();
        },

        /**
         * @private
         */
        _init: function () {
            this._super();
            var self = this;
            $(self.element).collapsible(self.options.collapsible);

            mediaCheck({
                media: this.options.mediaBreakpoint,
                entry: $.proxy(function () {
                    this._toggleMobileMode();
                }, this),
                exit: $.proxy(function () {
                    this._toggleDesktopMode();
                }, this)
            });
        },


        /**
         * @private
         */
        _toggleMobileMode: function () {
            var self = this;
            var element = $(self.element);
            element.collapsible('deactivate');
            element.collapsible('option', 'collapsible', true);
        },

        /**
         * @private
         */
        _toggleDesktopMode: function () {
            var self = this;
            var element = $(self.element);
            element.collapsible('activate');
            element.collapsible('option', 'collapsible', false);

        }
    });

    return $.formage.collapsibleBlock;
});

/*
 * @copyright Copyright (c) 2018 - 2019 adam@intw.pl
 */

define([
    'jquery',
    'slick'
], function($) {
    "use strict";

    $.widget('formage.ForMageSlider', {

        options: {
            slick : {
                lazyLoad: 'ondemand',
                slidesToShow: 1,
                dots: true,
                arrows: false,
                autoplay: true,
                autoplaySpeed: 2500,
                infinite: false,
                fade: true,
                cssEase: 'linear'
            }
        },

        _create: function() {
            this._initBlock();
        },

        _initBlock: function () {
            var self = this;

            self.element.slick(
                self.options.slick
            );
        }
    });
    return $.formage.ForMageSlider;
});
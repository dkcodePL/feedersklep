define([
    'jquery',
    'slick',
    'jquery-ui-modules/widget'
], function ($) {

    $.widget('formage.slickCarousel', {

        options: {
            delay: false,
            slick : {
                slidesToShow: 6,
                dots: false,
                mobileFirst: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 5
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 640,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 1,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            }
        },

        _create: function() {
            var self = this;

            if (!self.options.delay) {
                self._initBlock();
                return;
            }

            self._initClick(self.options.prevArrow, self.options.nextArrow, 'slickPrev');
            self._initClick(self.options.nextArrow, self.options.prevArrow, 'slickNext');
        },

        _initBlock: function () {
            this.element.slick(this.options.slick);
        },
        _initClick: function (element, elementToRemove, action) {
            var self = this;
            $(element).click(function () {
                $(this).remove();
                $(elementToRemove).remove();
                self._initBlock();
                self.element.slick(action);
            });
        },
    });

    return $.formage.slickCarousel;
});

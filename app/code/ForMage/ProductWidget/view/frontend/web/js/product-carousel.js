define([
	'jquery',
	'mage/url',
	'jquery-ui-modules/widget',
	'jquery-ui-modules/spinner',
	'slick',
	'Magento_Catalog/js/catalog-add-to-cart'
], function($, urlBuilder) {
	"use strict";

	$.widget('formage.productCarousel', {

			options: {
					delay: false,
					ajax: false,
					slider: true,
					loadingClass: 'loading',
					cartForm: "form[data-role='tocart-form']",
					children: '.items',
					url: 'productab/product/load/type/carousel/category_id/',
					slick : {
							lazyLoad: 'ondemand',
							slidesToShow: 1,
							dots: false,
							mobileFirst: true
					}
			},

			_create: function() {
					var self = this;

					if (self.options.ajax) {
							self._loadBlock();
							return;
					}

					if (!self.options.delay) {
							self._initBlock();
							return;
					}

					self._initClick(self.options.prevArrow, self.options.nextArrow, 'slickPrev');
					self._initClick(self.options.nextArrow, self.options.prevArrow, 'slickNext');

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
			_loadBlock: function () {
					var self = this;
					self.element.addClass(self.options.loadingClass);
					$.post(urlBuilder.build(self.options.url + self.options.category_id), function (html) {

							self.element.html(html);

					}).done(function() {
							self._initBlock();
							self.element.removeClass(self.options.loadingClass);
					});
			},
			_initBlock: function () {
					var self = this;

					self.element.children(self.options.children).each(function () {
							$(this).find(self.options.cartForm).catalogAddToCart();
					});

					if (self.options.slider) {
							self.element.slick(self.options.slick);
					}
			}
	});
	return $.formage.productCarousel;
});

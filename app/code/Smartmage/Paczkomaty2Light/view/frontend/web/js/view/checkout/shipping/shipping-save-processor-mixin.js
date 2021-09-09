define([
        'jquery',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/select-billing-address',
        'mage/translate'
    ],
    function ($, quote, storage, paymentService, methodConverter, resourceUrlManager, fullScreenLoader, errorProcessor, selectBillingAddressAction, $t) {
        'use strict';
        return function (target) {

            var saveShippingInformationParent = target.saveShippingInformation;

            target.saveShippingInformation = function () {
                if((quote.shippingMethod().method_code == 'paczkomaty2' || quote.shippingMethod().method_code == 'paczkomaty2cod')) {
					if(!$('#paczkomaty_point').val() && quote.shippingMethod().method_code == 'paczkomaty2' ||
                        !$('#paczkomaty_point_cod').val() && quote.shippingMethod().method_code == 'paczkomaty2cod'
                    ) {
						fullScreenLoader.stopLoader();
						alert($t('Please choose a paczkomat!'));
						return false;
					}
					
					// Drugie sprawdzenie w razie gdyby klient zmienił metodę dostawy bez zmiany punktu
					var dataToSend = {
						'machine': jQuery('#paczkomaty_point').val(),
						'payment': quote.shippingMethod().method_code == 'paczkomaty2' ? 'prepayment' : 'cod'
					};
					var isError = false;
					$.ajax({
						type: "POST",
						async: false,
						url: '/paczkomaty/index/index',
						data: dataToSend,
						dataType: 'json',
						showLoader: true
					}).done(function(data) {
						if(data.status !== 1) {
							alert(data.message);
							isError = true;
						}
						if (data.is_pop && quote.shippingMethod().method_code == 'paczkomaty2cod')
                        {
                            alert($t('POPs don\'t support cash on delivery'));
                            isError = true;
                        }
					});
					if(isError === true) {
						return false;
					}
                }

                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }

                var result = saveShippingInformationParent.apply();

                fullScreenLoader.startLoader();

                var payload = {
                    addressInformation: {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code,
                    }
                };

                return storage.post(
                    resourceUrlManager.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                        fullScreenLoader.stopLoader();
                    }
                ).fail(
                    function (response) {
                        errorProcessor.process(response);
                        fullScreenLoader.stopLoader();
                    }
                );

                return result;
            }
            return target;
        };
    });
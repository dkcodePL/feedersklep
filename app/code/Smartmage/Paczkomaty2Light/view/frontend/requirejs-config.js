var config = {
    paths: {
        "smpaczkomatySdk": [
            "https://geowidget.easypack24.net/js/sdk-for-javascript",
            "Smartmage_Paczkomaty2Light/js/sdk-for-javascript"
        ]
    },
    "config": {
        "mixins": {
            "Magento_Checkout/js/model/shipping-save-processor/default": {
                "Smartmage_Paczkomaty2Light/js/view/checkout/shipping/shipping-save-processor-mixin": true
            }
        }
    }
};
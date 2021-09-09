define(
    [
        'jquery'
        ,'mage/translate'
    ],
    function ($,$t) {
        "use strict";
        return {
            getRules: function() {
                return {
                    'email': {
                        'required': true
                    },
                    'telephone': {
                        'required': true
                    }
                };
            }
        };
    }
);
define(
    [
        'mage/url',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (url, fullScreenLoader) {
        'use strict';

        return {
            redirectUrl: 'syspay/placeorder/handler',

            execute: function () {
                fullScreenLoader.startLoader();
                window.location.replace(url.build(this.redirectUrl));
            }
        };
    }
);

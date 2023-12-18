define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'syspay',
                component: 'SysPay_Payment/js/view/payment/method-renderer/syspay'
            }
        );
        return Component.extend({});
    }
);

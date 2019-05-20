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
                type: 'paytabs',
                component: 'Ibnab_PayTabs/js/view/payment/method-renderer/paytabs'
            }
        );
        return Component.extend({});
    }
);
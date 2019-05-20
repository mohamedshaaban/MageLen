/*browser:true*/
/*global define*/
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
                type: 'paytabsexpress',
                component: 'Paytabs_Paytabsexpress/js/view/payment/method-renderer/paytabsexpress-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);

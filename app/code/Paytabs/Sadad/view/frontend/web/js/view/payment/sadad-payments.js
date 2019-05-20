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
                type: 'sadad',
                component: 'Paytabs_Sadad/js/view/payment/method-renderer/sadad-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);

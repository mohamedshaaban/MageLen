<?php

namespace Ibnab\PayTabs\Controller\Checkout;

class Index extends \Ibnab\PayTabs\Controller\AbstractCheckoutAction
{

    /**
     *
     * @return void
     */
    public function execute()
    {
        $order = $this->getOrder();
        if (isset($order)) {
            $payTabsModel = $this->getPayTabsModel();
            $payTabsModel->customCapture($order->getPayment());
            $redirectStatus = $this->getCheckoutSession()->getPaypageStatus();
            $redirectUrl = $this->getCheckoutSession()->getPaypagePaymentUrl();
            if (isset($redirectUrl) && $redirectStatus) {
                $this->getResponse()->setRedirect($redirectUrl);
            } else {
                $this->redirectToCheckoutFragmentPayment();
            }
        }
    }
}
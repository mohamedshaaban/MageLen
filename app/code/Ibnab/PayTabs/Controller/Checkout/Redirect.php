<?php


namespace Ibnab\PayTabs\Controller\Checkout;

/**
 * Return Action Controller (used to handle Redirects from the Payment Gateway)
 *
 * Class Redirect
 * @package Ibnab\PayTabs\Controller\Checkout
 */
class Redirect extends \Ibnab\PayTabs\Controller\AbstractCheckoutRedirectAction
{
    protected $_redirectBlockType = 'paytabs/server_redirect';
    protected $PTHost = "https://www.paytabs.com"; //https://stg.paytabs.net";
    /**
     * Handle the result from the Payment Gateway
     *
     * @return void
     */
    public function execute()
    {
        $response = $this->getRequest()->getParams();
        $payTabsModel = $this->getPayTabsModel();
        if(isset($response['payment_reference'])){
        $payment_reference = $response['payment_reference'];
        
        $fields = array(
            'merchant_email' => $this->getMerchantEmail(),
            'secret_key' => $this->getSecretKey(),
            'payment_reference' => $payment_reference
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $gateway_url = $this->PTHost . "/apiv2/verify_payment";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gateway_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $ch_result = curl_exec($ch);
        $ch_error = curl_error($ch);
        //print_r($ch_result);
        $dec = json_decode($ch_result, true);
        if (isset($payment_reference) && !empty($payment_reference) && $dec['response_code'] == 100) {

            $payTabsModel->afterSuccessOrder($response);
               $this->executeSuccessAction();
            return;
        } else {
                $payTabsModel->updateStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                $this->getMessageManager()->addWarning(
                    __("You have successfully canceled your order")
                );
                $this->executeCancelAction();
        }}
        else{
                $payTabsModel->updateStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
                $this->getMessageManager()->addWarning(
                    __("You have successfully canceled your order")
                );
                $this->executeCancelAction();
        }
    }
    
}

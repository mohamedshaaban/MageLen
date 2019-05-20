<?php

class Ras_Paytabs_ServerController extends Ras_Paytabs_Controller_Abstract {

    protected $_redirectBlockType = 'paytabs/server_redirect';
    protected $PTHost = "https://www.paytabs.com"; //https://stg.paytabs.net";

    public function responseAction() {
        $response = $this->getRequest()->getParams();
        $payment_reference = $response['payment_reference'];
        $secret_key = Mage::getSingleton('core/session')->getSecretKey();
        $merchant_email = Mage::getSingleton('core/session')->getMerchantEmail();

        $fields = array(
            'merchant_email' => $merchant_email,
            'secret_key' => $secret_key,
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

            Mage::getModel('paytabs/server')->afterSuccessOrder($response);
            $this->_redirect('checkout/onepage/success');
            return;
        } else {
            //If Payment has ERROR
            Mage::getModel('paytabs/server')->updateStatus(Mage_Sales_Model_Order::STATE_CANCELED);
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__($dec['result']));
            $this->_redirect('checkout/cart');
        }
    }

}

?>

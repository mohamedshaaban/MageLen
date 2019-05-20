<?php

namespace Ibnab\PayTabs\Model;

use \Magento\Framework\UrlInterface;
use \Magento\Payment\Model\InfoInterface;

/**
 * Pay In Store payment method model
 */
class PayTabs extends \Magento\Payment\Model\Method\AbstractMethod {

    /**
     * Payment code
     *
     * @var string
     */
    const CODE = 'paytabs';

    protected $_code = self::CODE;
    //protected $_infoBlockType = 'Ibnab\PayTabs\Block\Info\Cc';
    protected $_paymentUrl = '';
    protected $_paypageStatus = false;
    public $PTauthentication = true;
    protected $PTHost = "https://www.paytabs.com";
    protected $_isGateway = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_stripeApi = false;
    protected $_countryFactory;
    protected $_minAmount = null;
    protected $_maxAmount = null;
    protected $_supportedCurrencyCodes = array('USD');
    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];
    protected $urlBuilder;
    protected $localeResolver;
    public $storeManager;
    protected $_orderInstance;
    protected $ch_session;
    protected $_paymentMethod = 'server';
    protected $_transaction;        
    protected $_dbTransaction;   
    protected $_invoiceSender; 
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry,\Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,\Magento\Framework\DB\Transaction $dbTransaction,\Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory, \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory, \Magento\Payment\Helper\Data $paymentData, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Payment\Model\Method\Logger $logger, \Magento\Directory\Api\CountryInformationAcquirerInterface $countryFactory, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Framework\Session\SessionManagerInterface $big_session, \Magento\Framework\Locale\Resolver $localeResolver, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Sales\Api\Data\OrderInterface $orderInstance, \Magento\Checkout\Model\Session $ch_session, \Magento\Sales\Model\Order\Payment\Transaction $transaction, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = array()
    ) {
        parent::__construct(
                $context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data
        );
        $this->_countryFactory = $countryFactory;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->big_session = $big_session;
        $this->localeResolver = $localeResolver;
        $this->_orderInstance = $orderInstance;
        $this->ch_session = $ch_session;
        $this->_transaction = $transaction;
        $this->_dbTransaction = $dbTransaction;
        $this->_invoiceSender = $invoiceSender;
    }

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    private function _getAmount() {
        $total = $this->_order->getGrandTotal();
        return $total;
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getMerchantEmail() {
        $merchant_email = $this->getConfigData('merchant_email');
        return $merchant_email;
    }

    public function getSecretKey() {
        $secret_key = $this->getConfigData('secret_key');
        return $secret_key;
    }
    public function getOrderStatus() {
        $order_status = $this->getConfigData('order_status');
        return $order_status;
    }
    public function validate() {
        return true;
    }

    public function getOrderPlaceRedirectUrl() {
        $url = $this->urlBuilder->getDirectUrl('paytabs/' . $this->_paymentMethod . '/redirect');
        if (!$url) {
            $url = $this->PTHost . '/apiv2/create_pay_page';
        }
        return $url;
    }

    //Get 3 Digit ISO Code for Country
    public function _getISO3Code($szISO2Code) {
        $boFound = false;
        $nCount = 1;
        $collection = $this->_countryFactory->getCountriesInfo();
        while ($boFound == false &&
        $nCount < count($collection)) {
            $item = $collection[$nCount];
            if ($item->getTwoLetterAbbreviation() == $szISO2Code) {
                $boFound = true;
                $szISO3Code = $item->getThreeLetterAbbreviation();
            }
            $nCount++;
        }

        return $szISO3Code;
    }

    public function PT_validatesecretkey() {
        $authentication_URL = $this->PTHost . "/apiv2/validate_secret_key";

        $fields = array(
            'merchant_email' => $this->getMerchantEmail(),
            'secret_key' => $this->getSecretKey()
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $fields_string = substr($fields_string, 0, strrpos($fields_string, '&'));


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $authentication_URL);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $ch_result = curl_exec($ch);
        $ch_error = curl_error($ch);

        $dec = json_decode($ch_result, true);
        return $dec;
    }

    /**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {
        return parent::isAvailable($quote);
    }

    /**
     * Availability for currency
     *
     * @param string $currencyCode
     * @return bool
     */
    public function canUseForCurrency($currencyCode) {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function authorize(InfoInterface $payment, $amount) {

 
        $this->_logger->info(('from autorize'));        
        return $this;
    }

    public function updateStatus($status = \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,$order = null) {
        if($order == null){
        $orderId = $this->ch_session->getLastRealOrderId();
        $currentOrder = $this->_orderInstance->loadByIncrementId($orderId);
        }else{

        $currentOrder = $this->_orderInstance->loadByIncrementId($order->getId());
        }
        $currentOrder->setState($status, true)
            ->setStatus(
                    $status
            )->save();
    }

    public function statusOfPaypage() {
        return $this->_paypageStatus;
    }

    /**
     * Get url of Paytabs Payment
     *
     * @return string
     */
    public function getPaytabsCreatePayPageUrl() {
        // 1 - Test
        // 2 - Live
        /*
         * if( Mage::getStoreConfig('payment/' . $this->getCode() . '/environment')==1 ){
          $url = "http://paytabs.com/api/create_pay_page";
          } else
         * 
         * 
         */
        $url = $this->PTHost . "/apiv2/create_pay_page";
        return $url;
    }

    public function getPaytabsTransactionUrl() {
        /* if (!$this->_paymentUrl) {
          $this->_paymentUrl = $this->PTHost."/api/create_pay_page";
          } */
        return $this->_paymentUrl;
    }
    public function capture(InfoInterface $payment, $amount) {
        return $this;
    }            
    public function customCapture(InfoInterface $payment) {
       $this->_order = $payment->getOrder();
        $fields = "";
        /** @var \Magento\Sales\Model\Order\Address $billing */
        $billing = $this->_order->getBillingAddress();

        $auth = $this->PT_validatesecretkey();
        if (!$auth) {
            $this->_PTauthentication = false;
        }

        $secret_key = $this->getSecretKey();
        $merchant_email = $this->getMerchantEmail();

        $this->big_session->setSecretKey($secret_key);
        $this->big_session->setMerchantEmail($merchant_email);

        $fieldsArr = array();
        $lengs = 0;

        $serverip = "158.69.218.193"; //$_SERVER['SERVER_ADDR'];
        $customerip = $_SERVER['REMOTE_ADDR'];

        //Language Configuration
        $languageArray_arabic = array("ar_AE", "ar_BH", "ar_DZ", "ar_EG", "ar_IQ", "ar_JO", "ar_KW", "ar_LB", "ar_LY", "ar_MA", "ar_OM", "ar_QA", "ar_SA", "ar_SD", "ar_SY", "ar_TN", "ar_YE");
        $language_code = "English";
        $store_lang = $this->localeResolver->getLocale();
        if (in_array($store_lang, $languageArray_arabic)) {
            $language_code = "Arabic";
        }

        $current_currency_code = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $returnurl = $this->urlBuilder->getDirectUrl('paytabs/checkout/redirect', array('_secure' => false));
        $orderid = $this->_order->getRealOrderId();

        $shipping_amount = $this->_order->getShippingAmount();
        $discount_amount = abs($this->_order->getDiscountAmount());
        //	$orderDetail = Mage::getModel('sales/order')->loadByIncrementId($orderid); 	
        $order = $this->_orderInstance->loadByIncrementId($orderid);
        $this->updateStatus($this->getOrderStatus());
        $items = $order->getAllItems();
        $itemcount = count($items);
        $name = array();
        $unitPrice = array();
        $sku = array();
        $ids = array();
        $qty = array();
        $sumofproductprices = 0;
        foreach ($items as $itemId => $item) {
            if ($item->getQtyToInvoice() !== 0) {
                $name[] = $item->getName();
                $unitPrice[] = $item->getPrice();
                $sku[] = $item->getSku(); //Description of Product
                $ids[] = $item->getProductId();
                $qty[] = $item->getQtyToInvoice();
                $sumofproductprices += $item->getQtyToInvoice() * $item->getPrice();
            }
        }
        $othercharges = $this->_getAmount() + $discount_amount - $sumofproductprices;
        $amount_to_sent = $sumofproductprices + $othercharges;

        $b_address = $order->getBillingAddress()->getData();
        $s_address = ($order->getShippingAddress()) ? $order->getShippingAddress()->getData() : $order->getBillingAddress()->getData();
        $categoryName = '';
        $categoryNames = 'Mobile';
        foreach ($ids as $ID) {
            //$product = Mage::getModel('catalog/product')->load($ID);
            //$categoryIds[] = $product->getCategoryIds();
            //$_category = Mage::getModel('catalog/category')->load($categoryIds[0][0]);
            //$categoryNames = $_category->getName();
//                            $categoryNames = Mage::getModel('catalog/category')->load($categoryIds[0]);
            break;
        }
        $price_str_Arr = implode(" || ", $unitPrice);
        $product_str_Arr = implode(" || ", $name);
        $quantity_str_Arr = implode(" || ", $qty);
        $shipping_method = $order->getShippingMethod();
        $customer_name = $b_address['firstname'] . " " . $b_address['lastname']; //used for reference
        $site_url = "http://www.amazonybox.com"; //$this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        //$startDate = $this->storeManager 
        $fields = array(
            'merchant_email' => $this->getMerchantEmail(),
            'secret_key' => $secret_key,
            'cc_first_name' => $b_address['firstname'],
            'cc_last_name' => $b_address['lastname'],
            'phone_number' => $b_address['telephone'],
            'cc_phone_number' => $b_address['telephone'],
            'billing_address' => $b_address['street'],
            'city' => $b_address['city'],
            'state' => (empty($b_address['region'])) ? "N/A" : $b_address['region'],
            'postal_code' => (empty($b_address['postcode'])) ? "0000" : $b_address['postcode'],
            'country' => $this->_getISO3Code($b_address['country_id']), //$country,
            'email' => $b_address['email'],
            'amount' => $amount_to_sent,
            'other_charges' => $othercharges,
            'discount' => $discount_amount,
            'currency' => $current_currency_code,
            'title' => ($customer_name != "") ? $customer_name : $orderid,
            'ip_customer' => $customerip,
            'ip_merchant' => $serverip,
            'site_url' => rtrim($site_url, '/'),
            'return_url' => $returnurl,
            'address_shipping' => $s_address['street'],
            'city_shipping' => $s_address['city'],
            'state_shipping' => (empty($s_address['region'])) ? "N/A" : $s_address['region'],
            'postal_code_shipping' => (empty($b_address['postcode'])) ? "0000" : $s_address['postcode'],
            'country_shipping' => $this->_getISO3Code($s_address['country_id']),
            'quantity' => $quantity_str_Arr,
            'unit_price' => $price_str_Arr,
            'products_per_title' => $product_str_Arr,
            'ChannelOfOperations' => 'ChannelOfOperations',
            'ProductCategory' => $categoryNames,
            'ProductName' => $product_str_Arr,
            'ShippingMethod' => $shipping_method,
            'DeliveryType' => 'normal',
            'CustomerId' => $b_address['telephone'],
            'cms_with_version' => "magento-2",
            'msg_lang' => $language_code,
            'reference_no' => $orderid,
            'is_recurrence_payments' => "TRUE",
            "recurrence_start_date" => "24/04/2015",
            "recurrence_frequency" => "24",
            "recurrence_billing_cycle" => "monthly"
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $fields_string = substr($fields_string, 0, strrpos($fields_string, '&'));
//                $fields_string = urlencode($fields_string);
//                print_r($fields);
        //$this->_logger->error(__('start .... ' . implode(" ", $fields)));
        $gateway_url = $this->getPaytabsCreatePayPageUrl();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gateway_url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $ch_result = curl_exec($ch);
        $ch_error = curl_error($ch);
        $this->_logger->info(('autorize'.' '.$ch_result));
        //curl_getinfo($ch);
        //curl_close($ch);
        $dec = json_decode($ch_result, true);
        $errorMessage = "";
        if (isset($dec['response_code']) && $dec['response_code'] == "4012") {
            $this->_paymentUrl = $dec['payment_url'];
            $this->_paypageStatus = true;
            $this->ch_session->setPaypageStatus($this->_paypageStatus);
            $this->ch_session->setPaypagePaymentUrl($this->_paymentUrl);
            //$payment->setStatus(self::STATUS_APPROVED)
                   // ->setLastTransId($this->getTransactionId());
        } else {
            switch ($dec['response_code']) {
                case "4001":
                    $errorMessage = "Variable not found.";
                    break;
                case "4002":
                    $errorMessage = "Invalid Credentials.";
                    break;
                case "4007":
                    $errorMessage = "Missing parameter.";
                    break;
                case "4012":
                    $errorMessage = "Pay Page is created. User must go to the page to complete the payment.";
                    break;
                case "0404":
                    $errorMessage = "You don't have permissions to create an Invoice.";
                    break;
                default:
                    $errorMessage = "Something Went Wrong with Payment Information";
                    break;
            }
            $this->_logger->error(__($errorMessage));
        }
        
        return $this;
    }

    public function cancel(InfoInterface $payment) {
        $payment->setStatus(self::STATUS_DECLINED)
                ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function void(InfoInterface $payment) {
        // payment void code

        return $this;
    }

    public function refund(InfoInterface $payment, $amount) {
        $transactionId = $payment->getParentTransactionId();
        $payment
                ->setTransactionId($transactionId . '-' . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
                ->setParentTransactionId($transactionId)
                ->setIsTransactionClosed(1)
                ->setShouldCloseParentTransaction(1);
        return $this;
    }

    /*
     * Return redirect block type
     *
     * @return string
     */

    public function getRedirectBlockType() {
        return $this->_redirectBlockType;
    }
/*
    public function assignData(Magento\Framework\DataObject $data) {
        //Mage::throwException(implode(',',$data));
        $result = parent::assignData($data);
             if (is_array($data)) {
          $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
          }
          elseif ($data instanceof Varien_Object) {
          $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
          } 
        return $result;
    }
*/
    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType() {
        return $this->_paymentMethod;
    }

    public function getReturnURLonError() {
        $this->getMessageManager()->addError(
                __("There was Error in the Transaction.")
        );

        return $this->urlBuilder->getDirectUrl('checkout/cart');
    }

    public function afterSuccessOrder($response) {

        $orderId = $this->ch_session->getLastRealOrderId();
        $order = $this->_orderInstance;
        $order->loadByIncrementId($orderId);
        $paymentInst = $order->getPayment()->getMethodInstance();

        $paymentInst->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($orderId)
                ->setTransactionId($response['payment_reference']);

       // $order->sendNewOrderEmail();
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            // Save the invoice to the order
            $transaction = $this->_dbTransaction
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());

            $transaction->save();

            // Magento\Sales\Model\Order\Email\Sender\InvoiceSender
            $this->_invoiceSender->send($invoice);
        }
        $this->_transaction->setTxnId($response['payment_reference']);
        $this->_transaction->setOrderPaymentObject($order->getPayment())->setData(\Magento\Sales\Api\Data\TransactionInterface::PAYMENT_ID,$order->getPayment()->getId())->setData(\Magento\Sales\Api\Data\TransactionInterface::ORDER_ID,$order->getId())
                ->setTxnType(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
        $this->_transaction->save();
        $order_status = __('Payment is successful.');

        $order->addStatusToHistory(\Magento\Sales\Model\Order::STATE_PROCESSING, $order_status);
        $order->addStatusHistoryComment(
                        __('Notified customer about invoice #%1.', $order->prepareInvoice()->getId())
                )
                ->setIsCustomerNotified(true);
        $order->save();
    }

}

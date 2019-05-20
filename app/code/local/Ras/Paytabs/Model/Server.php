<?php
/**
 * Paytabs payment gateway
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 *
 * @category Ras
 * @package    Ras_Paytabs
 * @copyright  Copyright (c) 2013 Paytabs
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ras_Paytabs_Model_Server extends Mage_Payment_Model_Method_Abstract
{
    protected $_code  = 'paytabs_server';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;

    protected $_formBlockType = 'paytabs/server_form';
    protected $_paymentMethod = 'server';
    protected $_infoBlockType = 'paytabs/payment_info';

    protected $_order;
    
    protected $_paymentUrl = '';
    protected $_paypageStatus = false;
    public $PTauthentication = true;
    protected $PTHost = "https://www.paytabs.com";

    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')
                            ->loadByIncrementId($paymentInfo->getOrder()->getRealOrderId());
        }
        return $this->_order;
    }
    
    /**
     * Grand total getter
     *
     * @return string
     */
    private function _getAmount()
    {
        $total = $this->getOrder()->getGrandTotal();
        return $total;
    }

    /**
     * Get Customer Id
     *
     * @return string
     */
    public function getMerchantEmail()
    {
        $merchant_email = Mage::getStoreConfig('payment/' . $this->getCode() . '/merchant_email');            
        return $merchant_email;
    }

    public function getSecretKey()
    {
        $secret_key = Mage::getStoreConfig('payment/' . $this->getCode() . '/secret_key');
        return $secret_key;            
    }

    public function validate()
    {   
        return true;
    }
    
    public function getOrderPlaceRedirectUrl()
    {
        $url = Mage::getUrl('paytabs/' . $this->_paymentMethod . '/redirect');
        if(!$url) {
            $url = $this->PTHost.'/apiv2/create_pay_page';
        }
        return $url;
    }
    
    //Get 3 Digit ISO Code for Country
	public function _getISO3Code($szISO2Code) 
	{
		$boFound = false; 
		$nCount = 1; 

		$collection = Mage::getModel('directory/country_api')->items(); 

		while ($boFound == false && 
		$nCount < count($collection)) 
		{ 
		$item = $collection[$nCount]; 
		if($item['iso2_code'] == $szISO2Code) 
		{ 
		$boFound = true; 
		$szISO3Code = $item['iso3_code']; 
		} 
		$nCount++; 
		} 

		return $szISO3Code; 
	}
	
    public function _getccPhone($code){
        $countries = array(
          "AF" => '+93',//array("AFGHANISTAN", "AF", "AFG", "004"),
          "AL" => '+355',//array("ALBANIA", "AL", "ALB", "008"),
          "DZ" => '+213',//array("ALGERIA", "DZ", "DZA", "012"),
          "AS" => '+376',//array("AMERICAN SAMOA", "AS", "ASM", "016"),
          "AD" => '+376',//array("ANDORRA", "AD", "AND", "020"),
          "AO" => '+244',//array("ANGOLA", "AO", "AGO", "024"),
          "AG" => '+1-268',//array("ANTIGUA AND BARBUDA", "AG", "ATG", "028"),
          "AR" => '+54',//array("ARGENTINA", "AR", "ARG", "032"),
          "AM" => '+374',//array("ARMENIA", "AM", "ARM", "051"),
          "AU" => '+61',//array("AUSTRALIA", "AU", "AUS", "036"),
          "AT" => '+43',//array("AUSTRIA", "AT", "AUT", "040"),
          "AZ" => '+994',//array("AZERBAIJAN", "AZ", "AZE", "031"),
          "BS" => '+1-242',//array("BAHAMAS", "BS", "BHS", "044"),
          "BH" => '+973',//array("BAHRAIN", "BH", "BHR", "048"),
          "BD" => '+880',//array("BANGLADESH", "BD", "BGD", "050"),
          "BB" => '1-246',//array("BARBADOS", "BB", "BRB", "052"),
          "BY" => '+375',//array("BELARUS", "BY", "BLR", "112"),
          "BE" => '+32',//array("BELGIUM", "BE", "BEL", "056"),
          "BZ" => '+501',//array("BELIZE", "BZ", "BLZ", "084"),
          "BJ" =>'+229',// array("BENIN", "BJ", "BEN", "204"),
          "BT" => '+975',//array("BHUTAN", "BT", "BTN", "064"),
          "BO" => '+591',//array("BOLIVIA", "BO", "BOL", "068"),
          "BA" => '+387',//array("BOSNIA AND HERZEGOVINA", "BA", "BIH", "070"),
          "BW" => '+267',//array("BOTSWANA", "BW", "BWA", "072"),
          "BR" => '+55',//array("BRAZIL", "BR", "BRA", "076"),
          "BN" => '+673',//array("BRUNEI DARUSSALAM", "BN", "BRN", "096"),
          "BG" => '+359',//array("BULGARIA", "BG", "BGR", "100"),
          "BF" => '+226',//array("BURKINA FASO", "BF", "BFA", "854"),
          "BI" => '+257',//array("BURUNDI", "BI", "BDI", "108"),
          "KH" => '+855',//array("CAMBODIA", "KH", "KHM", "116"),
          "CA" => '+1',//array("CANADA", "CA", "CAN", "124"),
          "CV" => '+238',//array("CAPE VERDE", "CV", "CPV", "132"),
          "CF" => '+236',//array("CENTRAL AFRICAN REPUBLIC", "CF", "CAF", "140"),
          "CM" => '+237',//array("CENTRAL AFRICAN REPUBLIC", "CF", "CAF", "140"),
          "TD" => '+235',//array("CHAD", "TD", "TCD", "148"),
          "CL" => '+56',//array("CHILE", "CL", "CHL", "152"),
          "CN" => '+86',//array("CHINA", "CN", "CHN", "156"),
          "CO" => '+57',//array("COLOMBIA", "CO", "COL", "170"),
          "KM" => '+269',//array("COMOROS", "KM", "COM", "174"),
          "CG" => '+242',//array("CONGO", "CG", "COG", "178"),
          "CR" => '+506',//array("COSTA RICA", "CR", "CRI", "188"),
          "CI" => '+225',//array("COTE D'IVOIRE", "CI", "CIV", "384"),
          "HR" => '+385',//array("CROATIA (local name: Hrvatska)", "HR", "HRV", "191"),
          "CU" => '+53',//array("CUBA", "CU", "CUB", "192"),
          "CY" => '+357',//array("CYPRUS", "CY", "CYP", "196"),
          "CZ" => '+420',//array("CZECH REPUBLIC", "CZ", "CZE", "203"),
          "DK" => '+45',//array("DENMARK", "DK", "DNK", "208"),
          "DJ" => '+253',//array("DJIBOUTI", "DJ", "DJI", "262"),
          "DM" => '+1-767',//array("DOMINICA", "DM", "DMA", "212"),
          "DO" => '+1-809',//array("DOMINICAN REPUBLIC", "DO", "DOM", "214"),
          "EC" => '+593',//array("ECUADOR", "EC", "ECU", "218"),
          "EG" => '+20',//array("EGYPT", "EG", "EGY", "818"),
          "SV" => '+503',//array("EL SALVADOR", "SV", "SLV", "222"),
          "GQ" => '+240',//array("EQUATORIAL GUINEA", "GQ", "GNQ", "226"),
          "ER" => '+291',//array("ERITREA", "ER", "ERI", "232"),
          "EE" => '+372',//array("ESTONIA", "EE", "EST", "233"),
          "ET" => '+251',//array("ETHIOPIA", "ET", "ETH", "210"),
          "FJ" => '+679',//array("FIJI", "FJ", "FJI", "242"),
          "FI" => '+358',//array("FINLAND", "FI", "FIN", "246"),
          "FR" => '+33',//array("FRANCE", "FR", "FRA", "250"),
          "GA" => '+241',//array("GABON", "GA", "GAB", "266"),
          "GM" => '+220',//array("GAMBIA", "GM", "GMB", "270"),
          "GE" => '+995',//array("GEORGIA", "GE", "GEO", "268"),
          "DE" => '+49',//array("GERMANY", "DE", "DEU", "276"),
          "GH" => '+233',//array("GHANA", "GH", "GHA", "288"),
          "GR" => '+30',//array("GREECE", "GR", "GRC", "300"),
          "GD" => '+1-473',//array("GRENADA", "GD", "GRD", "308"),
          "GT" => '+502',//array("GUATEMALA", "GT", "GTM", "320"),
          "GN" => '+224',//array("GUINEA", "GN", "GIN", "324"),
          "GW" => '+245',//array("GUINEA-BISSAU", "GW", "GNB", "624"),
          "GY" => '+592',//array("GUYANA", "GY", "GUY", "328"),
          "HT" => '+509',//array("HAITI", "HT", "HTI", "332"),
          "HN" => '+504',//array("HONDURAS", "HN", "HND", "340"),
          "HK" => '+852',//array("HONG KONG", "HK", "HKG", "344"),
          "HU" => '+36',//array("HUNGARY", "HU", "HUN", "348"),
          "IS" => '+354',//array("ICELAND", "IS", "ISL", "352"),
          "IN" => '+91',//array("INDIA", "IN", "IND", "356"),
          "ID" => '+62',//array("INDONESIA", "ID", "IDN", "360"),
          "IR" => '+98',//array("IRAN, ISLAMIC REPUBLIC OF", "IR", "IRN", "364"),
          "IQ" => '+964',//array("IRAQ", "IQ", "IRQ", "368"),
          "IE" => '+353',//array("IRELAND", "IE", "IRL", "372"),
          "IL" => '+972',//array("ISRAEL", "IL", "ISR", "376"),
          "IT" => '+39',//array("ITALY", "IT", "ITA", "380"),
          "JM" => '+1-876',//array("JAMAICA", "JM", "JAM", "388"),
          "JP" => '+81',//array("JAPAN", "JP", "JPN", "392"),
          "JO" => '+962',//array("JORDAN", "JO", "JOR", "400"),
          "KZ" => '+7',//array("KAZAKHSTAN", "KZ", "KAZ", "398"),
          "KE" => '+254',//array("KENYA", "KE", "KEN", "404"),
          "KI" => '+686',//array("KIRIBATI", "KI", "KIR", "296"),
          "KP" => '+850',//array("KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF", "KP", "PRK", "408"),
          "KR" => '+82',//array("KOREA, REPUBLIC OF", "KR", "KOR", "410"),
          "KW" => '+965',//array("KUWAIT", "KW", "KWT", "414"),
          "KG" => '+996',//array("KYRGYZSTAN", "KG", "KGZ", "417"),
          "LA" => '+856',//array("LAO PEOPLE'S DEMOCRATIC REPUBLIC", "LA", "LAO", "418"),
          "LV" => '+371',//array("LATVIA", "LV", "LVA", "428"),
          "LB" => '+961',//array("LEBANON", "LB", "LBN", "422"),
          "LS" => '+266',//array("LESOTHO", "LS", "LSO", "426"),
          "LR" => '+231',//array("LIBERIA", "LR", "LBR", "430"),
          "LY" => '+218',//array("LIBYAN ARAB JAMAHIRIYA", "LY", "LBY", "434"),
          "LI" => '+423',//array("LIECHTENSTEIN", "LI", "LIE", "438"),
          "LU" => '+352',//array("LUXEMBOURG", "LU", "LUX", "442"),
          "MO" => '+389',//array("MACAU", "MO", "MAC", "446"),
          "MG" => '+261',//array("MADAGASCAR", "MG", "MDG", "450"),
          "MW" => '+265',//array("MALAWI", "MW", "MWI", "454"),
          "MY" => '+60',//array("MALAYSIA", "MY", "MYS", "458"),     
          "MX" => '+52',//array("MEXICO", "MX", "MEX", "484"),
          "MC" => '+377',//array("MONACO", "MC", "MCO", "492"),
          "MA" => '+212',//array("MOROCCO", "MA", "MAR", "504"),
          "NP" => '+977',//array("NEPAL", "NP", "NPL", "524"),
          "NL" => '+31',//array("NETHERLANDS", "NL", "NLD", "528"),
          "NZ" => '+64',//array("NEW ZEALAND", "NZ", "NZL", "554"),
          "NI" => '+505',//array("NICARAGUA", "NI", "NIC", "558"),
          "NE" => '+227',//array("NIGER", "NE", "NER", "562"),
          "NG" => '+234',//array("NIGERIA", "NG", "NGA", "566"),
          "NO" => '+47',//array("NORWAY", "NO", "NOR", "578"),
          "OM" => '+968',//array("OMAN", "OM", "OMN", "512"),
          "PK" => '+92',//array("PAKISTAN", "PK", "PAK", "586"),
          "PA" => '+507',//array("PANAMA", "PA", "PAN", "591"),
          "PG" => '+675',//array("PAPUA NEW GUINEA", "PG", "PNG", "598"),
          "PY" =>'+595',// array("PARAGUAY", "PY", "PRY", "600"),
          "PE" =>'+51',// array("PERU", "PE", "PER", "604"),
          "PH" =>'+63',// array("PHILIPPINES", "PH", "PHL", "608"),
          "PL" => '48',//array("POLAND", "PL", "POL", "616"),
          "PT" => '+351',//array("PORTUGAL", "PT", "PRT", "620"),
          "QA" => '+974',//array("QATAR", "QA", "QAT", "634"),
          "RU" => '+7',//array("RUSSIAN FEDERATION", "RU", "RUS", "643"),
          "RW" => '+250',//array("RWANDA", "RW", "RWA", "646"),
          "SA" => '+966',//array("SAUDI ARABIA", "SA", "SAU", "682"),
          "SN" => '+221',//array("SENEGAL", "SN", "SEN", "686"),
          "SG" => '+65',//array("SINGAPORE", "SG", "SGP", "702"),
          "SK" => '+421',//array("SLOVAKIA (Slovak Republic)", "SK", "SVK", "703"),
          "SI" => '+386',//array("SLOVENIA", "SI", "SVN", "705"),
          "ZA" => '+27',//array("SOUTH AFRICA", "ZA", "ZAF", "710"),
          "ES" => '+34',//array("SPAIN", "ES", "ESP", "724"),
          "LK" => '+94',//array("SRI LANKA", "LK", "LKA", "144"),
          "SD" => '+249',//array("SUDAN", "SD", "SDN", "736"),
          "SZ" => '+268',//array("SWAZILAND", "SZ", "SWZ", "748"),
          "SE" => '+46',//array("SWEDEN", "SE", "SWE", "752"),
          "CH" => '+41',//array("SWITZERLAND", "CH", "CHE", "756"),
          "SY" => '+963',//array("SYRIAN ARAB REPUBLIC", "SY", "SYR", "760"),
          "TZ" => '+255',//array("TANZANIA, UNITED REPUBLIC OF", "TZ", "TZA", "834"),
          "TH" => '+66',//array("THAILAND", "TH", "THA", "764"),
          "TG" => '+228',//array("TOGO", "TG", "TGO", "768"),
          "TO" => '+676',//array("TONGA", "TO", "TON", "776"),
          "TN" => '+216',//array("TUNISIA", "TN", "TUN", "788"),
          "TR" => '+90',//array("TURKEY", "TR", "TUR", "792"),
          "TM" => '+993',//array("TURKMENISTAN", "TM", "TKM", "795"),
          "UA" => '+380',//array("UKRAINE", "UA", "UKR", "804"),
          "AE" => '+971',//array("UNITED ARAB EMIRATES", "AE", "ARE", "784"),
          "GB" => '+44',//array("UNITED KINGDOM", "GB", "GBR", "826"),
          "US" => '+1'//array("UNITED STATES", "US", "USA", "840"),
          
        );

    
      return $countries[$code];
    }
        
        public function PT_validatesecretkey() {
            $authentication_URL = $this->PTHost."/apiv2/validate_secret_key";
            
            $fields = array(
                'merchant_email' => $this->getMerchantEmail(),
                'secret_key' => $this->getSecretKey()
            );
            $fields_string = "";
            foreach($fields as $key=>$value) {
                $fields_string .= urlencode($key).'='.urlencode($value).'&'; 
            }
            $fields_string = substr($fields_string, 0, strrpos($fields_string, '&'));
            
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$authentication_URL);
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $ch_result = curl_exec($ch);
            $ch_error = curl_error($ch); 
            
            $dec = json_decode($ch_result,true);
            return $dec;
        }
	
    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields() {
        //Perform Authentication
        $auth = $this->PT_validatesecretkey();
        if (!$auth) {
            $this->_PTauthentication = false;
        }

        $secret_key = $this->getSecretKey();
        $merchant_email = $this->getMerchantEmail();
        Mage::getSingleton('core/session')->setSecretKey($secret_key);
        Mage::getSingleton('core/session')->setMerchantEmail($merchant_email);

        $fieldsArr = array();

        $paymentInfo = $this->getInfoInstance();
        $lengs = 0;

        $serverip = $_SERVER['SERVER_ADDR'];
        $customerip = $_SERVER['REMOTE_ADDR'];

        //Language Configuration
        $languageArray_arabic = array("ar_AE", "ar_BH", "ar_DZ", "ar_EG", "ar_IQ", "ar_JO", "ar_KW", "ar_LB", "ar_LY", "ar_MA", "ar_OM", "ar_QA", "ar_SA", "ar_SD", "ar_SY", "ar_TN", "ar_YE");
        $language_code = "English";
        $store_lang = Mage::app()->getLocale()->getLocaleCode();
        if(in_array($store_lang, $languageArray_arabic)) {
            $language_code = "Arabic";
        }

        $current_currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $returnurl = Mage::getUrl('paytabs/' . $this->_paymentMethod . '/response', array('_secure' => true));
        $orderid = $paymentInfo->getOrder()->getRealOrderId();

        $shipping_amount = $paymentInfo->getOrder()->getShippingAmount();
        $discount_amount = abs($paymentInfo->getOrder()->getDiscountAmount());
        //	$orderDetail = Mage::getModel('sales/order')->loadByIncrementId($orderid); 	
        $order = Mage::getModel('sales/order')->loadByIncrementID($orderid);
        $this->updateStatus(Mage_Sales_Model_Order::STATE_NEW);
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
        $othercharges = $this->_getAmount()+$discount_amount - $sumofproductprices;
        $amount_to_sent = $sumofproductprices + $othercharges;
//        $othercharges = abs($othercharges + $shipping_amount);
        $categoryName = '';
        $categoryNames = 'Mobile';
        foreach ($ids as $ID) {
            $product = Mage::getModel('catalog/product')->load($ID);
            $categoryIds[] = $product->getCategoryIds();
            $_category = Mage::getModel('catalog/category')->load($categoryIds[0][0]);
            $categoryNames = $_category->getName();
//                            $categoryNames = Mage::getModel('catalog/category')->load($categoryIds[0]);
            break;
        }

        $b_address = $order->getBillingAddress()->getData();
        $s_address = ($order->getShippingAddress()) ? $order->getShippingAddress()->getData() : $order->getBillingAddress()->getData();

        $price_str_Arr = implode(" || ", $unitPrice);
        $product_str_Arr = implode(" || ", $name);
        $quantity_str_Arr = implode(" || ", $qty);
        $shipping_method = $order->getShippingMethod();
        $customer_name = $b_address['firstname'] . " " . $b_address['lastname']; //used for reference
        $site_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $fields = array(
            'merchant_email' => $this->getMerchantEmail(),
            'secret_key' => $secret_key,
            'cc_first_name' => $b_address['firstname'],
            'cc_last_name' => $b_address['lastname'],
            'phone_number' => $b_address['telephone'],
            'cc_phone_number' => $this->_getccPhone($b_address['country_id']),
            'billing_address' => mb_substr($b_address['street'],0,39),
            'city' => $b_address['city'],
            'state' => (empty($b_address['region']))?"N/A":$b_address['region'],
            'postal_code' => (empty($b_address['postcode']))?"0000":$b_address['postcode'],
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
            'address_shipping' => mb_substr($s_address['street'],0,39),
            'city_shipping' => $s_address['city'],
            'state_shipping' => (empty($s_address['region']))?"N/A":$s_address['region'],
            'postal_code_shipping' => (empty($b_address['postcode']))?"0000":$s_address['postcode'],
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
            'cms_with_version' => "magento-" . Mage::getVersion(),
            'msg_lang' => $language_code,
            'reference_no' => $orderid
        );
        $fields_string = "";
        foreach ($fields as $key => $value) {
            $fields_string .= urlencode($key) . '=' . urlencode($value) . '&';
        }
        $fields_string = substr($fields_string, 0, strrpos($fields_string, '&'));
//                $fields_string = urlencode($fields_string);
//                print_r($fields);
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
        Mage::log($ch_result);
        //curl_getinfo($ch);
        //curl_close($ch);
        $dec = json_decode($ch_result, true);
        $errorMessage = "";
        if (isset($dec['response_code']) && $dec['response_code'] == "4012") {
            $this->_paymentUrl = $dec['payment_url'];
            $this->_paypageStatus = true;
            return;
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

            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Something went wrong. Please Contact the Website Administrator for more information.'));
            Mage::log($errorMessage);
            return;
        }

        //return;
    }

    public function statusOfPaypage(){
        return $this->_paypageStatus;
    }
    /**
     * Get url of Paytabs Payment
     *
     * @return string
     */
    public function getPaytabsCreatePayPageUrl()
    {
		// 1 - Test
		// 2 - Live
        /*
         * if( Mage::getStoreConfig('payment/' . $this->getCode() . '/environment')==1 ){
			$url = "http://paytabs.com/api/create_pay_page";
            } else 
         * 
         * 
         */
        $url = $this->PTHost."/apiv2/create_pay_page";
         return $url;
         
    }
    
    public function getPaytabsTransactionUrl()
    {
         /*if (!$this->_paymentUrl) {
             $this->_paymentUrl = $this->PTHost."/api/create_pay_page";
         }*/
         return $this->_paymentUrl;
    }

    /**
     * Get debug flag
     *
     * @return string
     */
    public function getDebug()
    {
        return Mage::getStoreConfig('payment/' . $this->getCode() . '/debug_flag');
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setLastTransId($this->getTransactionId());

        return $this;
    }

    /**
     * Return redirect block type
     *
     * @return string
     */
    public function getRedirectBlockType()
    {
        return $this->_redirectBlockType;
    }

    public function assignData($data)
    {
        //Mage::throwException(implode(',',$data));
        $result = parent::assignData($data);        
        /*if (is_array($data)) {
            $this->getInfoInstance()->setAdditionalInformation($key, isset($data[$key]) ? $data[$key] : null);
        }
        elseif ($data instanceof Varien_Object) {
            $this->getInfoInstance()->setAdditionalInformation($key, $data->getData($key));
        }*/
        return $result;
    }
    /**
     * Return payment method type string
     *
     * @return string
     */
    public function getPaymentMethodType()
    {
        return $this->_paymentMethod;
    }

    public function getReturnURLonError()
    {
        Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('There was Error in the Transaction.'));
//        return Mage::app()->getFrontController()->getResponse()->setRedirect();
        return Mage::getUrl('checkout/cart');
    }
    
    public function afterSuccessOrder($response)
    {
		
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);

        $paymentInst = $order->getPayment()->getMethodInstance();

        $paymentInst->setStatus(self::STATUS_APPROVED)
                ->setLastTransId($orderId)
                ->setTransactionId($response['payment_reference']);

        $order->sendNewOrderEmail();
        if ($order->canInvoice()) {
            $invoice = $order->prepareInvoice();

            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder())
                    ->save();
        }
        $transaction = Mage::getModel('sales/order_payment_transaction');
        $transaction->setTxnId($response['payment_reference']);
        $transaction->setOrderPaymentObject($order->getPayment())
                ->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
        $transaction->save();
        $order_status = Mage::helper('core')->__('Payment is successful.');

        $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_PROCESSING, $order_status);
        $order->save();
    }
    
    public function updateStatus($status = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT){
        $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderId);
        $order->setState($status, true)->save();
    }
}

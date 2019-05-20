<?php
/** 
 * @copyright  PayTabs
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Paytabs\Paytabsexpress\Controller\Ipn;

use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Framework\App\Action\Action as AppAction;

class Callback extends AppAction
{
    /**
    * @var \Citrus\Icp\Model\PaymentMethod
    */
    protected $_paymentMethod;

    /**
    * @var \Magento\Sales\Model\Order
    */
    protected $_order;

    /**
    * @var \Magento\Sales\Model\OrderFactory
    */
    protected $_orderFactory;

    /**
    * @var Magento\Sales\Model\Order\Email\Sender\OrderSender
    */
    protected $_orderSender;

    /**
    * @var \Psr\Log\LoggerInterface
    */
    protected $_logger;
	
	protected $request;

    /**
    * @param \Magento\Framework\App\Action\Context $context
    * @param \Magento\Sales\Model\OrderFactory $orderFactory
    * @param \Citrus\Icp\Model\PaymentMethod $paymentMethod
    * @param Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender
    * @param  \Psr\Log\LoggerInterface $logger
    */
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
	\Magento\Framework\App\Request\Http $request,
    \Magento\Sales\Model\OrderFactory $orderFactory,
    \Paytabs\Paytabsexpress\Model\PaymentMethod $paymentMethod,
    \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,	
    \Psr\Log\LoggerInterface $logger
    ) {
        $this->_paymentMethod = $paymentMethod;
        $this->_orderFactory = $orderFactory;
        $this->_client = $this->_paymentMethod->getClient();
        $this->_orderSender = $orderSender;		
        $this->_logger = $logger;	
		$this->request = $request;
        parent::__construct($context);
    }

    /**
    * Handle POST request to Citrus callback endpoint.
    */
    public function execute()
    {
        try {
            // Cryptographically verify authenticity of callback
            if($this->getRequest ()->isPost ())
			{				
				$this->_success();
				$this->paymentAction();
			}
			else
			{
	            $this->_logger->addError("Paytabs: no post back data received in callback");
				return $this->_failure();
			}
        } catch (Exception $e) {
            $this->_logger->addError("Paytabs: error processing callback");
            $this->_logger->addError($e->getMessage());
            return $this->_failure();
        }
		
		$this->_logger->addInfo("Paytabs Transaction END from Paytabs");
    }
	
	protected function paymentAction()
	{
		$signature = "";
		$reqsignature = "";
		$data = "";
		$flag = "dataValid";
		$respdata = "";
		$orderid = "-1";
		
		$merchantid =$this->_paymentMethod->getConfigData ( 'merchantid' );
		$secretkey = $this->_paymentMethod->getConfigData ( 'secretkey' );
		$secretsign = $this->_paymentMethod->getConfigData ( 'secretsign' );
		
		
		$postdata = $this->request->getPost();
					
		if (isset($postdata ['transaction_id'])) {
				
			$pdata = $postdata;
				
			$txnrefno = $pdata ['transaction_id'];
			$respdata .= "<br/><strong>PayTabs Transaction Id: </strong>" . $txnrefno;
			
			$txnid = $pdata['order_id'];
			$orderid=$txnid;
			$this->_loadOrder($orderid);
			$TxStatus = $pdata ['response_code'];
			$respdata .= "<br/><strong>Response Code: </strong>" . $TxStatus;
			$respdata .= "<br/><strong>Response Message: </strong>" . $pdata['response_message'];

			$customerName = $pdata ['customer_name'];
			$respdata .= "<br/><strong>Customer Name: </strong>" . $customerName;
			$customerEmail = $pdata ['customer_email'];
			$respdata .= "<br/><strong>Customer Email: </strong>" . $customerEmail;
			
			$last_4_digits = $pdata['last_4_digits'];
			
			$currency = $pdata ['transaction_currency'];
			$txMsg = "Response Message: ".$pdata['response_message']." Transaction Id: ".$txnrefno;
			$amount = $pdata['transaction_amount'];
			
			$params = array(
				'order_id' => $pdata['order_id'],				
				'response_code' => $pdata['response_code'],
				'customer_name' => $pdata['customer_name'],
				'transaction_currency' => $pdata['transaction_currency'],
				'last_4_digits' => $pdata['last_4_digits'],
				'customer_email' => $pdata['customer_email']
			);		
			$calSignature = $this->create_secure_hash($params, $secretsign);
				
		
			$signature = $pdata ['secure_sign'];
										
			if($calSignature != "" && strcmp($signature, $calSignature) != 0)
			{
			 	$flag = "dataTampered";
			}
				
			$this->_logger->addInfo("PayTabs Ref No. is " . $txnrefno . " Response Code " . $TxStatus . " Full Response - " . $txMsg );
		
			if($pdata['response_code'] == 100)
			{
				if($flag != "dataValid")
				{	
		        	//$this->_order->hold()->save();
					$this->_createPaytabsComment("Paytabs Response signature does not match. You might have received tampered data", true);
					$this->_order->cancel()->save();

					$this->_logger->addError("Paytabs Response signature did not match ");

					//AA display error to customer = where ???
					$this->messageManager->addError("<strong>Error:</strong> Paytabs Response signature does not match. You might have received tampered data");
					$this->_redirect('checkout/onepage/failure');
				
				}else{			
					$this->_registerPaymentCapture($txnrefno, $amount, $txMsg);
					//$this->_logger->addInfo("Paytabs Response Order success..".$txMsg);
				
					$redirectUrl = $this->_paymentMethod->getSuccessUrl();
					//AA Where 
					$this->_redirect($redirectUrl);
				}
			}
			else
			{
				$historymessage = $txMsg;
				
				$this->_createPaytabsComment($historymessage);
				$this->_order->cancel()->save();				

				//$this->_logger->addInfo("Paytabs Response Order cancelled ..");
			
				$this->messageManager->addError("<strong>Error:</strong> $txMsg <br/>");
				//AA where 
				$redirectUrl = $this->_paymentMethod->getCancelUrl();
				$this->_redirect($redirectUrl);
			}		
		}
	}
	

	//AA - To review - required 
    protected function _registerPaymentCapture($transactionId, $amount, $message)
    {
        $payment = $this->_order->getPayment();
		
		
        $payment->setTransactionId($transactionId)       
        ->setPreparedMessage($this->_createPaytabsComment($message))
        ->setShouldCloseParentTransaction(true)
        ->setIsTransactionClosed(0)
		->setAdditionalInformation(['paytabsexpress','expresscheckout'])		
        ->registerCaptureNotification(
		//AA
            $amount,
            true 
        );

        $this->_order->save();

        $invoice = $payment->getCreatedInvoice();
        if ($invoice && !$this->_order->getEmailSent()) {
            $this->_orderSender->send($this->_order);
            $this->_order->addStatusHistoryComment(
                __('You notified customer about invoice #%1.', $invoice->getIncrementId())
            )->setIsCustomerNotified(
                true
            )->save();
        }
    }

	//AA Done
    protected function _loadOrder($order_id)
    {
        $this->_order = $this->_orderFactory->create()->loadByIncrementId($order_id);

        if (!$this->_order && $this->_order->getId()) {
            throw new Exception('Could not find Magento order with id $order_id');
        }
    }

	//AA Done
    protected function _success()
    {
        $this->getResponse()
             ->setStatusHeader(200);
    }

	//AA Done
    protected function _failure()
    {
        $this->getResponse()
             ->setStatusHeader(400);
    }

    /**
    * Returns the generated comment or order status history object.
    *
    * @return string|\Magento\Sales\Model\Order\Status\History
    */
	//AA Done
    protected function _createPaytabsComment($message = '')
    {       
        if ($message != '')
        {
            $message = $this->_order->addStatusHistoryComment($message);
            $message->setIsCustomerNotified(null);
        }
		
        return $message;
    }
	
	protected function create_secure_hash($params = array(), $shain_phrase) 
	{
		$string = ''; // SHA phrase
		ksort($params); // Sorting the params
		foreach ($params as $keys => $values)
		{
			$string .= strtoupper($keys) . '=' . $values . $shain_phrase;
			// Combining the field values in one string
		}
		// SHA Converting here
		$secure_sign = sha1($string); // Converting the values to SHA1 format
		return $secure_sign;
	}
}

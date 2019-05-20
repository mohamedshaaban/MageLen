<?php

namespace Ibnab\PayTabs\Controller;

/**
 * Base Checkout Redirect Controller Class
 * Class AbstractCheckoutRedirectAction
 * @package Ibnab\PayTabs\Controller
 */
abstract class AbstractCheckoutRedirectAction extends \Ibnab\PayTabs\Controller\AbstractCheckoutAction
{
    /**
     * @var \Ibnab\PayTabs\Helper\Checkout
     */
    private $_checkoutHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ibnab\PayTabs\Helper\Checkout $checkoutHelper,
        \Ibnab\PayTabs\Model\PayTabs $payTabsModel
    ) {
        parent::__construct($context, $logger, $checkoutSession, $orderFactory,$payTabsModel);
        $this->_checkoutHelper = $checkoutHelper;
    }
    public function getMerchantEmail() {
        return $this->getPayTabsModel()->getMerchantEmail();
    }

    public function getSecretKey() {
        return $this->getPayTabsModel()->getSecretKey();
    }
    /**
     * Get an Instance of the Magento Checkout Helper
     * @return \Ibnab\PayTabs\Helper\Checkout
     */
    protected function getCheckoutHelper()
    {
        return $this->_checkoutHelper;
    }

    /**
     * Handle Success Action
     * @return void
     */
    protected function executeSuccessAction()
    {
        if ($this->getCheckoutSession()->getLastRealOrderId()) {
            $this->getMessageManager()->addSuccess(__("Your payment is complete"));
            $this->redirectToCheckoutOnePageSuccess();
        }
    }

    /**
     * Handle Cancel Action from Payment Gateway
     */
    protected function executeCancelAction()
    {
        $this->getCheckoutHelper()->cancelCurrentOrder('');
        //$this->getCheckoutHelper()->restoreQuote();
        $this->redirectToCheckoutCart();
    }

    /**
     * Get the redirect action
     *      - success
     *      - cancel
     *      - failure
     *
     * @return string
     */
    protected function getReturnAction()
    {
        return $this->getRequest()->getParam('action');
    }
}

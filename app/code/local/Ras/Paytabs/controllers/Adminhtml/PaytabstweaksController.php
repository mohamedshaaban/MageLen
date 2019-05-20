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
class Ras_Paytabs_Adminhtml_PaytabstweaksController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function checkAction()
    {
        $result = "Invalid Credentials";
        //Verify Username Password
        $response = Mage::getModel('paytabs/server')->PT_validatesecretkey();
        if(isset($response) && $response['result']=="valid" && isset($response['response_code']) && $response['response_code']=="4000") {
            $result = "Credentials Verified";
        }
        Mage::app()->getResponse()->setBody($result);
    }
}

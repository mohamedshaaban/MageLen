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

class Ras_Paytabs_Block_Server_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
            $server = $this->getOrder()->getPayment()->getMethodInstance();
            $formFields = $server->getFormFields();
            //If Paypage Created, then Forward; 
            // If False, Return to website showing an Error.
            if($server->PTauthentication && $server->statusOfPaypage()){
                $form = new Varien_Data_Form();
                //$form->setAction($server->getPaytabsCreatePayPageUrl())
//                $form->setId('paytabs_server_checkout')
//                    ->setName('paytabs_server_checkout')
//                    ->setMethod('POST')
//                    ->setUseContainer(true);
//                foreach ($formFields as $field=>$value) {
//                    $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
//                }
//                $form->setAction($server->getPaytabsTransactionUrl());

                $html = '<html><head><meta charset="utf-8"></head><body>';
//                $html.= $this->__('You will be redirected to Paytabs Pay Page in a few seconds ...');
//                $html.= $form->toHtml();
//                $html.= '<script type="text/javascript">document.getElementById("paytabs_server_checkout").submit();</script>';
//                $html.= '</body></html>';
//                $html = str_replace('<div><input name="form_key" type="hidden" value="'.Mage::getSingleton('core/session')->getFormKey().'" /></div>','',$html);
                $html.= '<script type="text/javascript">window.location.href="'.$server->getPaytabsTransactionUrl().'";</script>';

                return $html;
            } else {
                Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
                return ;
            }

    }
}

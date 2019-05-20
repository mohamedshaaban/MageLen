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
 
class Ras_Paytabs_Block_Server_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
//                $locale = Mage::app()->getLocale();
//        $imageURL = "<img src='http://d1.saletab.com/cards.png'>";
        $mark = Mage::getConfig()->getBlockClassName('core/template');
        $mark = new $mark;
        $imageURL = "<img src='".$this->getSkinUrl('paytabs/cards.png')."'>";
//        $mark->setTemplate('paypal/payment/mark.phtml');
        $this->setTemplate('')
            ->setRedirectMessage(
                Mage::helper('paytabs')->__($this->__('pt_redirection_info'))
            )->setMethodTitle($this->__('pt_payment_tag')) // Output PayPal mark, omit title
            ->setMethodLabelAfterHtml($imageURL);
    }
    public function getCcAvailableTypes()
    {
        return array(
            'VI'=>'VISA', // VISA (VI)
            'MC'=>'MasterCard', // MasterCard (MC)
            'DC'=>'Diners Club', // Diners Club (DC)       
        );
        /*$types = $this->_getConfig()->getCcTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('amexcctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;*/
    }
}

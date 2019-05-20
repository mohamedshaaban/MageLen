<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vsourz\Ordercomment\Controller\Orderfiledelete;

class Index extends \Vsourz\Ordercomment\Controller\Index
{

    public function execute()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');

        $checkoutSession->setFileuploadvalue('');
        $checkoutSession->setOrderForFile('');
        $checkoutSession->setFileuploadvaluestatus('0');
        $this->_redirect("checkout");
    }
}

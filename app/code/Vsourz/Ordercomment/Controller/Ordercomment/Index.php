<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vsourz\Ordercomment\Controller\Ordercomment;

class Index extends \Vsourz\Ordercomment\Controller\Index
{
    public function execute()
    {
        $success = 0;
        $fileuplaodstatus = 0;
        $ordercommentsstatus = 0;
        $fileuplaodvalue = "";
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $checkoutSession = $objectManager->create('\Magento\Checkout\Model\Session');

        $checkoutSession->setOrderCommentsdata(1);
        $checkoutSession->setFileuploadvaluestatus(0);
        $image = $this->getRequest()->getFiles('order_for');

        $Ordercomments = $this->getRequest()->getPost('order_comments');
        if ($Ordercomments) {
            $checkoutSession->setOrderCommentstext($Ordercomments);

            $ordercommentsstatus = 1;
            $checkoutSession->setOrdercommentsstatus($ordercommentsstatus);
            $success = 1;
        } else {
            $Ordercomments = "";
            $checkoutSession->setOrderCommentstext($checkoutSession->getOrderCommentstext());
            $ordercommentsstatus = 1;
            $checkoutSession->setOrdercommentsstatus($ordercommentsstatus);
        }

        if (isset($image)) {

                $fileName = $image['name'];
                $fileName = str_replace(' ', '_', $fileName);
                $checkoutSession->setOrderForFile($fileName);

                $mediaDirectory = $this->_objectManager
                ->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

            try {
                    $OrdercommentFiletype = $objectManager
                    ->create('Vsourz\Ordercomment\Helper\Data')->getOrdercommentFiletype();
                    $uploader = $this->_objectManager
                    ->create('\Magento\MediaStorage\Model\File\Uploader', ['fileId'=>'order_for']);
                    if ($OrdercommentFiletype) {
                       $OrdercommentFiletypeArray = explode(',',$OrdercommentFiletype);
                       $uploader->setAllowedExtensions($OrdercommentFiletypeArray);
                    } else {
                       $uploader->setAllowedExtensions(['jpg','jpeg','gif','png','txt','exe','psd','csv','doc']);
                    }
                    $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
                    $uploader->setAllowRenameFiles(true);
                    $uploader->save($mediaDirectory->getAbsolutePath('vsourz/orderfileattachment'));

                    $fileuplaodstatus = 1;
                    $fileuplaodvalue = $fileName;
                    $checkoutSession->setFileuploadstatus($fileuplaodstatus);
                    $checkoutSession->setFileuploadvalue($fileuplaodvalue);
                    $checkoutSession->setFileuploadvaluestatus(0);

                    $success = 1;
            } catch (\Exception $e) {
                if ($e->getCode() == 0) {
                    $checkoutSession->setFileuploadstatus('');
                    $checkoutSession->setFileuploadvalue('');
                    $checkoutSession->setFileuploadvaluestatus(1);
                    $this->_redirect("checkout");
                }
            }
        } else {
            $checkoutSession->setOrderForFile('');
            $fileuplaodstatus = 1;
            $checkoutSession->setFileuploadstatus($fileuplaodstatus);
            $checkoutSession->setFileuploadvalue($checkoutSession->getFileuploadvalue());
            $checkoutSession->setFileuploadvaluestatus(0);
            $this->_redirect("checkout");
        }
            $this->_redirect("checkout");
    }
}

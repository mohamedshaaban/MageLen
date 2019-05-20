<?php
namespace Ibnab\PayTabs\Block\Info;

class Cc extends \Magento\Payment\Block\Info\Cc {

    protected $_template = 'Ibnab_PayTabs::info/cc.phtml';

    public function getAdditionalInformation($key = null) {
        return $this->getInfo()->getAdditionalInformation($key);
    }

}
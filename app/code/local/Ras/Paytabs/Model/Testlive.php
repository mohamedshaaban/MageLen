<?php 

class Ras_Paytabs_Model_Testlive
{
    public function toOptionArray()
    {
        return array(
			//array('value'=>'', 'label'=>Mage::helper('adminhtml')->__('Select Environment')),
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('Test')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('Live'))
        );
    }
}

?>

<?php
namespace Webkul\CompanyName\Module\Plugin\Checkout;
class LayoutProcessor
{
/**
* @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
* @param array $jsLayout
* @return array
*/
public function afterProcess(
\Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
array  $jsLayout
) {
$configuration = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];
foreach ($configuration as $paymentGroup => $groupConfig) {
if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {

$jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['company_tax_id'] = [
'component' => 'Magento_Ui/js/form/element/abstract',
'config' => [
'customScope' => 'billingAddress.custom_attributes',
'template' => 'ui/form/field',
'elementTmpl' => 'ui/form/element/input',
'options' => [],
'id' => 'custom-field'
],
'dataScope' => 'billingAddress.custom_attributes.custom_field',
'label' => 'Custom Field',
'provider' => 'checkoutProvider',
'visible' => true,
'validation' => [],
'sortOrder' => 250,
'id' => 'custom-field'
];
}
}


return $jsLayout;
}
}
?>

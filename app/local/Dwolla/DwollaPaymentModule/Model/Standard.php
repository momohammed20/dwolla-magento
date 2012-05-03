<?php
/**
 * Dwolla Payment Module for Magento > 1.5.0
 *
 * @category    Dwolla
 * @package     Dwolla_DwollaPaymentModule
 * @copyright   Copyright (c) 2012 Dwolla Inc. (http://www.dwolla.com)
 * @autor   	Michael Schonfeld <michael@dwolla.com>
 * @version   	2.0.0alpha
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Dwolla_DwollaPaymentModule_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'dwollaPaymentModule';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = true;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('dwollaPaymentModule/payment/redirect', array('_secure' => true));
	}
}
?>
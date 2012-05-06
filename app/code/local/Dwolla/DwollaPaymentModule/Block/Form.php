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

class Dwolla_DwollaPaymentModule_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Set template and redirect message
     */
    public function __construct()
    {
	    parent::__construct();
	    $this
		    ->setTemplate('dwollaPaymentModule/form.phtml')
			->setRedirectMessage(
				Mage::helper('paypal')->__('You will be redirected to the Dwolla website when you place an order.')
			);
    }
}

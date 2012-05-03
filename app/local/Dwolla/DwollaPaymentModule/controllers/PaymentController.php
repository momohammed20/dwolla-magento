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

class Dwolla_DwollaPaymentModule_PaymentController extends Mage_Core_Controller_Front_Action {
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','dwollaPaymentModule',array('template' => 'dwollaPaymentModule/redirect.phtml')); 
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}

	public function responseAction() {
		// Get User Configuration
		$dwollaId = Mage::getStoreConfig('payment/dwollaPaymentModule/dwollaId');
		$apiKey = Mage::getStoreConfig('payment/dwollaPaymentModule/dwollaApiKey');
		$apiSecret = Mage::getStoreConfig('payment/dwollaPaymentModule/dwollaApiSecret');

		// Grab Dwolla's Response
		$orderId = $this->getRequest()->orderid;
		$checkoutId = $this->getRequest()->checkoutid;
		$transactionId = $this->getRequest()->transaction;
		$amount = $this->getRequest()->amount;
		$signature = $this->getRequest()->signature;

		// Check if the user cancelled, or
		// if there was any other error
		$isError = $this->getRequest()->error;
		$errorDescription = $this->getRequest()->error_description;

		if($isError) {
			return $this->cancelAction($orderId, $errorDescription);
		}

		// Validate Dwolla's Signature
		$string = "{$checkoutId}&{$amount}";
		$hash = hash_hmac('sha1', $string, $apiSecret);
		$validated = ($hash == $signature);

		if($validated) {
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($orderId);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, "Gateway has authorized the payment (Dwolla Transaction ID {$transactionId} / Checkout ID {$checkoutId}).");

			$order->sendNewOrderEmail();
			$order->setEmailSent(true);

			$order->save();

			Mage::getSingleton('checkout/session')->unsQuoteId();

			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
		}
		else {
			$this->cancelAction(FALSE, 'Dwolla signature did not validate');
			Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
		}
	}

	public function cancelAction($orderId = FALSE, $errorDescription = "Unknown reason") {
		if(!$orderId) { $orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId(); }

        if ($orderId) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            if($order->getId()) {
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, "Gateway has declined the payment: {$errorDescription}.")->save();

				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
        }
	}
}
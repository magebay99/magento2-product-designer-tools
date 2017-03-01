<?php
namespace PDP\Integration\Block\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer {
	
	protected $_script = true;
	
	public function loadScript() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$pdpIntegrationSession = $objectManager->create('PDP\Integration\Model\Session');
		if($this->_script && $pdpIntegrationSession->getPdpCheckoutCart()) {
			//$pdpIntegrationSession->clearStorage();
			$pdpIntegrationSession->setPdpCheckoutCart(0);
			$this->_script = false;
			return true;
		} else {
			return false;
		}
	}
}
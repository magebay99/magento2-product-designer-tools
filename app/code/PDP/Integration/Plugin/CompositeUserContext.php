<?php
namespace PDP\Integration\Plugin;
use Magento\Authorization\Model\UserContextInterface;

class CompositeUserContext{
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	private $_storeManager;
	
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	private $customerSession;
	
	const REST_PDP_URL = 'rest/V1/pdpintegration/add';
	
	/**
	 * @param \Magento\Authorization\Model\CompositeUserContext $subject
	 * @param array $result
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function afterGetUserType(\Magento\Authorization\Model\CompositeUserContext $subject, $result) {
		$customerSession = $this->getCustomerSession();
		$_storeManager = $this->getStoreManagerInterface();
		if ($customerSession->isLoggedIn() && strpos($_storeManager->getStore()->getCurrentUrl(), self::REST_PDP_URL) !== false) {
			return UserContextInterface::USER_TYPE_CUSTOMER;
		} else {
			return $result;
		}
	}	
	
	/**
	 * @param \Magento\Authorization\Model\CompositeUserContext $subject
	 * @param array $result
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function afterGetUserId(\Magento\Authorization\Model\CompositeUserContext $subject, $result) {
		$customerSession = $this->getCustomerSession();
		$_storeManager = $this->getStoreManagerInterface();
		if ($customerSession->isLoggedIn() && strpos($_storeManager->getStore()->getCurrentUrl(), self::REST_PDP_URL) !== false) {
			return $customerSession->getCustomerId();
		} else {
			return $result;
		}
	}
	
    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     *
     * @deprecated
     */	
	private function getStoreManagerInterface() {
        if ($this->_storeManager === null) {
            $this->_storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Store\Model\StoreManagerInterface');
        }
        return $this->_storeManager;		
	}    
	
	/**
     * @return \Magento\Customer\Model\Session
     *
     * @deprecated
     */	
	private function getCustomerSession() {
        if ($this->customerSession === null) {
            $this->customerSession = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Customer\Model\Session');
        }
        return $this->customerSession;		
	}
}
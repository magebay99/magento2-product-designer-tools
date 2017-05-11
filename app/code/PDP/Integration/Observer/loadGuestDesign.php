<?php
namespace PDP\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;

class loadGuestDesign implements ObserverInterface {

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
	
    /**
     * PDP Integration session
     *
     * @var \PDP\Integration\Model\Session
     */
    protected $_pdpIntegrationSession;
	
    /**
     * @var \PDP\Integration\Model\PdpGuestDesignFactory
     */
    protected $_pdpGuestDesignFactory;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \PDP\Integration\Model\Session $pdpIntegrationSession
     * @param \PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
		\PDP\Integration\Model\Session $pdpIntegrationSession,
		\PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_customerSession = $customerSession;
		$this->_pdpIntegrationSession = $pdpIntegrationSession;
		$this->_pdpGuestDesignFactory = $pdpGuestDesignFactory;
        $this->messageManager = $messageManager;
    }
	
    /**
     * Customer login action
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return $this;
        }
		$pdpGuestDesignId = $this->_pdpIntegrationSession->getPdpDesignId();
		if($pdpGuestDesignId) {
			$this->_pdpIntegrationSession->setPdpDesignId(null);
			$customerId = $this->_customerSession->getCustomerId();
			if($customerId) {
				try {
					$_dataGuestDesign = $this->_pdpGuestDesignFactory->create()->load($pdpGuestDesignId);
					if($_dataGuestDesign->getEntityId()) {
						$_dataItemVal = unserialize($_dataGuestDesign->getItemValue());
					}
					$dataGuestDesign = $this->_pdpGuestDesignFactory->create()->loadByCustomerId($customerId);
					if($dataGuestDesign->getEntityId() && !$_dataGuestDesign->getCustomerId() && $_dataGuestDesign->getCustomerIsGuest() && $_dataGuestDesign->getEntityId()) {
						try {
							$this->_pdpGuestDesignFactory->create()->setId($pdpGuestDesignId)->delete();
							//$this->_pdpIntegrationSession->setPdpDesignId(null);
						} catch(\Exception $e) {
							$this->messageManager->addException($e, __('Load guest design error'));
						}
						$dataItemVal = unserialize($dataGuestDesign->getItemValue());
						if(isset($_dataItemVal)) {
							foreach($_dataItemVal as $_item) {
								$dataItemVal[] = $_item;
							}
						}
						$dataGuestDesign->setItemValue(serialize($dataItemVal))->save();									
					} else {
						if($_dataGuestDesign->getEntityId()) {
							$this->_pdpGuestDesignFactory->create()
														 ->load($pdpGuestDesignId)
														 ->setCustomerIsGuest(0)
														 ->setCustomerId($customerId)
														 ->save();
						}
					}
				} catch(\Exception $e) {
					$this->messageManager->addException($e, __('Load guest design error'));
				}
			}
		}
	}	
	
}
<?php
namespace PDP\Integration\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class PdpSalesOrder implements  ObserverInterface{

	/**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;
	
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }
    
	/**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */		
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$order = $observer->getEvent()->getOrder();
		$status = $order->getState();
		if($status == 'complete') {
			$orderId = $order->getEntityId();
			$pdpOrderRelation = $this->_objectManager->get('PDP\Integration\Model\PdpOrderRelationFactory')->create();
			$pdpOrder = $this->_objectManager->get('PDP\Integration\Model\PdpOrderFactory')->create();
			$dataOderRela = $pdpOrderRelation->getDataWithOrderId($orderId);
			if($dataOderRela) {
				foreach($dataOderRela as $vl) {
					$pdp_order_id = $vl['pdp_order_id'];
				}
				if($pdp_order_id) {
					$_data = $pdpOrder->load($pdp_order_id);
					if($_data->getOrderId()) {
						try {
							$_data->setOrderStatus($status)->save();
						} catch(\Exception $e) {
							throw new \Magento\Framework\Exception\LocalizedException(
								new \Magento\Framework\Phrase($e->getMessage())
							);
						}
					}
				}
			}
		}
		return $this;
	}
}
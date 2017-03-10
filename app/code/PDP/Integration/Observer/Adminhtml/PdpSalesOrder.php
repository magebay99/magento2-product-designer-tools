<?php
namespace PDP\Integration\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

class PdpSalesOrder implements  ObserverInterface{

	/**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;	
	
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_objectManager = $objectManager;
        $this->messageManager = $messageManager;
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
		if($status == 'complete' || $status == 'closed') {
			$orderId = $order->getEntityId();
			$pdpOrderRelation = $this->_objectManager->get('PDP\Integration\Model\PdpOrderRelationFactory')->create();
			$pdpOrder = $this->_objectManager->get('PDP\Integration\Model\PdpOrderFactory')->create();
			$dataOderRela = $pdpOrderRelation->getDataWithOrderId($orderId);
			if($dataOderRela) {
				foreach($dataOderRela as $vl) {
					if(isset($vl['pdp_order_id'])) {
						$pdp_order_id = $vl['pdp_order_id'];
					}
				}
				if(isset($pdp_order_id) && $pdp_order_id) {
					$_data = $pdpOrder->load($pdp_order_id);
					if($_data->getOrderId()) {
						try {
							$_data->setOrderStatus($status)->save();
						} catch(\Exception $e) {
							$this->messageManager->addException($e, __('Update pdp order error'));
						}
					}
				}
			}
		}
		return $this;
	}
}
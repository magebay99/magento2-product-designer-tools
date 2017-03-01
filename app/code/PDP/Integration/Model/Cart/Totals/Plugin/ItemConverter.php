<?php

namespace PDP\Integration\Model\Cart\Totals\Plugin;

class ItemConverter {

    /**
     * @var PdpquoteCollectionFactory
     */
    protected $pdpquoteCollectionFactory;
	
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;	

	/** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;			
	
    /**
     * @param \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */	
	public function __construct(
		\PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->pdpquoteCollectionFactory = $pdpquoteCollectionFactory;
		$this->jsonHelper = $jsonHelper;
		$this->storeManager = $storeManager;
	}
	
    /**
     * @param \Magento\Quote\Model\Cart\Totals\ItemConverter $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */	
	public function afterModelToDataObject(\Magento\Quote\Model\Cart\Totals\ItemConverter $subject, $result) {
		//if($result instanceof \PDP\Integration\Model\Cart\Totals\Item ) {
			//echo '<pre>';print_r($result);die;
			//$result->setPdpOptions('test pdp options 1212');
			//$result->setOptions('[{\"value\":\"dfdfmkmkmk\",\"label\":\"opt field\"}]');
		//}
		/*if(isset($items)) {
			$items['pdpoptions'] = '[]';
			if($items['item_id']) {
				$pdpItem = $this->getPdpCartItem($items['item_id']);
				$pdpItemArr = $pdpItem->getData();
				if(count($pdpItemArr)) {
					foreach($pdpItemArr as $item) {
						$_value = unserialize($item['value']);
						$items['pdpoptions'] = $this->jsonHelper->jsonEncode($_value);
					}
				}
			}
			$result = $items;
		}*/
		//if(!$result->getPdpoptions()) {
			$result->setPdpoptions('mkmk');
		//}
		return $result;
	}
	
    /**
     * @param int $itemId
	 *
	 * @return \PDP\Integration\Model\Pdpquote | null
     */	
	protected function getPdpCartItem($itemId) {
		$itemData = null;
		if($itemId) {
			$itemData = $this->pdpquoteCollectionFactory->create()
				 ->addFieldToFilter('item_id', array('eq' => $itemId));
		}
		return $itemData;
	}		
}
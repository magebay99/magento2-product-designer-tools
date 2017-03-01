<?php

namespace PDP\Integration\Plugin\Cart\Item;

class Renderer {

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
	
    /**
    * @var PdpquoteCollectionFactory
    */
    protected $pdpquoteCollectionFactory;

    /**
	* @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
    * 
    */
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
    ) {
        $this->storeManager = $storeManager;
		$this->pdpquoteCollectionFactory = $pdpquoteCollectionFactory;
    }	
	/**
	* 
	* @param \Magento\Checkout\Block\Cart\Item\Renderer $subject
	* @param array $result
	* @return array
	* @SuppressWarnings(PHPMD.UnusedFormalParameter)
	*/
	public function afterGetProductOptions(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result) {
		$item = $subject->getItem();
		if(is_object($item)) {
			$itemId = $item->getId();
			if($itemId) {
				$pdpCartItems = $this->getPdpCartItem($itemId);
				$pdpCartItemsArr = $pdpCartItems->getData();
				if(count($pdpCartItemsArr)) {
					foreach($pdpCartItemsArr as $pdpItem) {
						$valueObj = unserialize($pdpItem['value']);
						if(count($valueObj)) {
							foreach($valueObj as $vlItem) {
								$result[] = array(
									'label' => $vlItem['label'],
									'value' => $vlItem['value'],
									'print_value' => $vlItem['value'],
									'option_id' => '',
									'option_type' => '',
									'custom_view' => ''
								);
							}
						}
					}
				}
			}
		}
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
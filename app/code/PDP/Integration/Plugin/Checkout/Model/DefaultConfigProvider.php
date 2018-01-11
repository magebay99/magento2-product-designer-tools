<?php
namespace PDP\Integration\Plugin\Checkout\Model;

use Magento\Framework\Serialize\Serializer\Json;

class DefaultConfigProvider {
	
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
     * @var Json
     */
    private $serializer;	
	
    /**
     * @param \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param Json $serializer
	 */	
	public function __construct(
		\PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		Json $serializer = null
	) {
		$this->pdpquoteCollectionFactory = $pdpquoteCollectionFactory;
		$this->jsonHelper = $jsonHelper;
		$this->storeManager = $storeManager;
		$this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
	}
	
    /**
     * @param \Magento\Checkout\Block\Onepage $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result) {
		//if(is_array($result) || is_object($result)) {
			if(isset($result['quoteItemData']))$quoteItemData = $result['quoteItemData'];
			if(isset($result['totalsData'])) {
				//if(is_array($result['totalsData']) {
					if(isset($result['totalsData']['items']))$totalsDataItems = $result['totalsData']['items'];
				//}
			}
		//}
		if(isset($totalsDataItems)) {
			foreach($totalsDataItems as $_key => $item) {
				$totalsDataItems[$_key]['pdpoptions'] = '[]';
				if($item['item_id']) {
					$dataItem = $this->getPdpCartItem($item['item_id']);
					$dataItemArr = $dataItem->getData();
					if(count($dataItemArr)) {
						foreach($dataItemArr as $totalItem) {
							$_value = $this->serializer->unserialize($totalItem['value']);
							$totalsDataItems[$_key]['pdpoptions'] = $this->jsonHelper->jsonEncode($_value);
						}
					}
				}
			}			
		}
		if(isset($quoteItemData)) {
			foreach($quoteItemData as $key => $quote) {
				$quoteItemData[$key]['pdpoptions'] = array();
				if($quote['item_id']) {
					$pdpcartItems = $this->getPdpCartItem($quote['item_id']);
					$pdpcartItemArr = $pdpcartItems->getData();
					if(count($pdpcartItemArr)) {
						foreach($pdpcartItemArr as $pdpItem) {
							$valueObj = $this->serializer->unserialize($pdpItem['value']);
							$quoteItemData[$key]['pdpoptions'] = $valueObj;
						}
					}
				}
			}
		}
		if(isset($totalsDataItems))$result['totalsData']['items'] = $totalsDataItems;
		if(isset($quoteItemData)) $result['quoteItemData'] = $quoteItemData;
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


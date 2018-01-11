<?php
namespace PDP\Integration\Block\Adminhtml\Items\Column\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class DefaultColumn {
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
		$this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
	}

    /**
     * @param \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */	
	public function afterGetOrderOptions(\Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $subject, $result) {
		$orderItem = $subject->getItem();
		$itemId = $orderItem->getQuoteItemId();
		$_result = array();
		if($itemId) {
			$pdpItems = $this->getPdpCartItem($itemId);
			$pdpItemArr = $pdpItems->getData();
			if(count($pdpItemArr)) {				
				foreach($pdpItemArr as $pdpItem) {
					$valueObj = $this->serializer->unserialize($pdpItem['value']);
					if(count($valueObj)) {
						foreach($valueObj as $vlItem) {
							$_result[] = array(
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
		if(count($_result)) {
			$result = array_merge($_result, $result);
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
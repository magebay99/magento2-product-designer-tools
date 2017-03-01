<?php
namespace PDP\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;
use PDP\Integration\Helper\PdpOptions;

class PdpQuoteManagement implements ObserverInterface {

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;
	
    /**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * 
     */
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \PDP\Integration\Helper\PdpOptions $pdpOptions
    ) {
        $this->storeManager = $storeManager;
		$this->_pdpOptions = $pdpOptions;
    }
	
    /**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */	
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$order = $observer->getOrder();
		$items = $order->getAllVisibleItems();
		foreach($items as $item) {
			$quoteItemId = $item->getQuoteItemId();
			$_product = $item->getProduct();
			if($quoteItemId && $_product->getTypeId() == 'pdpro') {
				$pdpItemArr = $this->_pdpOptions->getPdpCartItem($quoteItemId);
				//$pdpItemArr = $pdpItems->getData();
				if(count($pdpItemArr)) {
					$requestOptions = $item->getProductOptionByCode('info_buyRequest');
					if(isset($requestOptions['pdp_options'])) {
						$pdpValue = unserialize($pdpItemArr[0]['value']);
						$pdpOptions = $pdpValue['pdp_options'];
						$pdp_print_type = $pdpValue['pdp_print_type'];
						$pdpOptSelect = $this->_pdpOptions->getOptionsSelect($pdpOptions);
						$additional_options = $this->_pdpOptions->getAdditionOption($pdpOptSelect);
						$requestOptions['quote_id'] = $quoteItemId;
						$additionalOptions['info_buyRequest'] = $requestOptions;
						if(count($pdp_print_type)) {
							$printType = array('label' => 'Print type', 'value' => '');
							$printType['value'] = $pdp_print_type['title'];
							//if($pdp_print_type['price']) $printTypePrice = $pdp_print_type['price'];
							$printTypeValue = $pdp_print_type['value'];
							$additional_options[] = $printType;
						}
						$additionalOptions['additional_options'] = $additional_options;
						$item->setProductOptions($additionalOptions);
						$item->save();
					}
				}
			}
		}
		return $this;
	}
}
<?php
namespace PDP\Integration\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use PDP\Integration\Helper\PdpOptions;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class PdpQuoteAddItem implements ObserverInterface{

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;
	
	/**
	* @var Magento\Framework\Pricing\Helper\Data;
	**/
	protected $_priceHelper;	
	
    /**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * 
     */
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		PriceHelper $priceHelper,
        \PDP\Integration\Helper\PdpOptions $pdpOptions
    ) {
        $this->storeManager = $storeManager;
		$this->_priceHelper = $priceHelper;
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
		$item = $observer->getQuoteItem();
		$item = ($item->getParentItem() ? $item->getParentItem() : $item);
		//get info_buyRequest 
		$buyInfor = $item->getBuyRequest();
		$_product = $item->getProduct();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$request = $objectManager->create('Magento\Framework\App\RequestInterface');
		$params = $request->getParams();
		if(isset($buyInfor['pdp_options']) && $_product->getTypeId() == \PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE) {
			if(isset($buyInfor['pdp_price']) && $buyInfor['pdp_price']) {
				$pdpoptPrice = $buyInfor['pdp_price'];
				$pdpoptPrice = $this->_priceHelper->currency($pdpoptPrice,false,false);
				$price = $pdpoptPrice;
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);
			}
		}
		return $this;
	}	
}
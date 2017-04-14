<?php
namespace PDP\Integration\Observer\Webapi;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class PdpItemPrice implements ObserverInterface {
    
	/**
     * @var UrlInterface
     */
    protected $_urlBuilder;
	
	/**
     * @var RequestInterface
     */
    protected $_request;
	
	/**
	 * @var Magento\Framework\Pricing\Helper\Data;
	 **/
	protected $_priceHelper;
	
    /**
     * @param UrlInterface $urlBuilder
	 * @param PriceHelper $priceHelper
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
		PriceHelper $priceHelper,
		\Magento\Framework\App\RequestInterface $request
    ) {
        $this->_urlBuilder = $urlBuilder;
		$this->_priceHelper = $priceHelper;
        $this->_request = $request;
    }
	
    /**
     * 
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */			
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$items = $observer->getEvent()->getData('items');
		$item = $items[0];
		$product = $item->getProduct();
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		$infoRequest = $item->getBuyRequest();
		if($product->getTypeId() == 'pdpro') {
			if(isset($infoRequest['pdp_price']) && $infoRequest['pdp_price']) {
				$pdpPrice = $infoRequest['pdp_price'];
				$pdpPrice = $this->_priceHelper->currency($pdpPrice,false,false);
				$productPrice = $product->getFinalPrice();
				$productPrice = $this->_priceHelper->currency($productPrice,false,false);
				$price = $productPrice + $pdpPrice;
				//$price = $product->getPrice() + $infoRequest['pdp_price'];
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);			
			}
		}
		return $this;
	}
}
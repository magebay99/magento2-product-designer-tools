<?php
namespace PDP\Integration\Observer\Webapi;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

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
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
		\Magento\Framework\App\RequestInterface $request
    ) {
        $this->_urlBuilder = $urlBuilder;
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
		//die('pdp options');
		$items = $observer->getEvent()->getData('items');
		$item = $items[0];
		$product = $item->getProduct();
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		$infoRequest = $item->getBuyRequest();
		if(isset($infoRequest['pdp_options'])) {
			if(isset($infoRequest['pdp_price']) && $infoRequest['pdp_price']) {
				$price = $product->getPrice() + $infoRequest['pdp_price'];
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);			
			}
		}
		return $this;
	}
}
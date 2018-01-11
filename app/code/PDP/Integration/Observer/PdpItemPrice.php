<?php
namespace PDP\Integration\Observer;

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
     * @param Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Magento\Framework\App\RequestInterface $request
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */			
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$item = $observer->getEvent()->getData('quote_item');
		$product = $observer->getEvent()->getData('product');
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		$infoRequest = $item->getBuyRequest();
		if($product->getTypeId() == \PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE) {
			if(isset($infoRequest['pdp_price']) && $infoRequest['pdp_price']) {
				$pdpPrice = $infoRequest['pdp_price'];
				$pdpPrice = $this->_priceHelper->currency($pdpPrice,false,false);
				$productPrice = $product->getFinalPrice();
				$productPrice = $this->_priceHelper->currency($productPrice,false,false);
				$price = $productPrice + $pdpPrice;
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);			
			}
		}
		return $this;
	}
	
	protected function getParams() {
		return $this->_request->getParams();
	}
}
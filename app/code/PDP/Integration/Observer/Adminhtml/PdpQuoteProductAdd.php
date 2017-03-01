<?php
namespace PDP\Integration\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class PdpQuoteProductAdd implements ObserverInterface{
	
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;
	
	/**
	* @var Magento\Framework\App\RequestInterface;
	**/
	protected $_request;
	
    /**
	* @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
    * @param \Magento\Framework\App\RequestInterface $request
    */
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \PDP\Integration\Helper\PdpOptions $pdpOptions,
		RequestInterface $request
    ) {
        $this->storeManager = $storeManager;
		$this->_pdpOptions = $pdpOptions;
		$this->_request = $request;
    }
	
	/* Magento\Quote\Model\Quote.php */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		$items = $observer->getItems();
		$params = $this->_request->getParams();
		foreach($items as $item) {
			$product = $item->getProduct();
			$buyRequest = isset($params['item'][$item->getId()]) ? $params['item'][$item->getId()] : array();
			if(isset($buyRequest['pdp_options']) && $product->getTypeId() == 'pdpro') {
				$dataRequest = serialize($buyRequest);
				$item->addOption(array('code'=> 'info_buyRequest', 'product_id'=> $item->getProductId(), 'value'=> $dataRequest));
				
			}
		}
		return $this;
	}
}
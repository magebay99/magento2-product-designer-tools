<?php
namespace PDP\Integration\Model\Sales\AdminOrder;

class Create extends \Magento\Sales\Model\AdminOrder\Create
{
	public function addProduct($product, $config = 1) {
        if (!is_array($config) && !$config instanceof \Magento\Framework\DataObject) {
            $config = ['qty' => $config];
        }
        $config = new \Magento\Framework\DataObject($config);

        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product;
            $product = $this->_objectManager->create(
                'Magento\Catalog\Model\Product'
            )->setStore(
                $this->getSession()->getStore()
            )->setStoreId(
                $this->getSession()->getStoreId()
            )->load(
                $product
            );
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('We could not add a product to cart by the ID "%1".', $productId)
                );
            }
        }
		$request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
		$params = $request->getParam('item');
		$buyRequest = isset($params[$product->getId()]) ? $params[$product->getId()] : array();
		$pdpOptions = $this->_objectManager->get('PDP\Integration\Helper\PdpOptions');
		//$pdpOptions = $pdpOptions->createExtractOptions($product,$buyRequest);
		if(isset($buyRequest['pdp_options'])) {
			
		}
		//$urlBuilder = $this->_objectManager->get('Magento\Framework\UrlInterface');
		/*$currentUrl  = $urlBuilder->getCurrentUrl();
		$items =$this->getQuote()->getAllVisibleItems();
		foreach($items as $_item) {
			$itemId = $_item->getItemId();
			if($itemId) {
				$pdpOptions = $this->_objectManager->get('PDP\Integration\Helper\PdpOptions');
				$pdpItems = $pdpOptions->getPdpCartItem($itemId);
				$pdpItemArr = $pdpItems->getData();
				if(count($pdpItemArr)) {
					//$params = $request->getParam('item');
					$additionalOptions = array();
					$additionalOptions[] = array(
									'label' => 'test pdp option',
									'value' => 'value test option',
					);
					$product->addCustomOption('additional_options', serialize($additionalOptions));
				}
			}
		}*/
        $item = $this->quoteInitializer->init($this->getQuote(), $product, $config);
        if (is_string($item)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($item));
        }
        $item->checkData();
        $this->setRecollect(true);

        return $this;		
	}
	
	 public function updateQuoteItems($items)
    {
		$request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
		$urlBuilder = $this->_objectManager->get('Magento\Framework\UrlInterface');
		$currentUrl  = $urlBuilder->getCurrentUrl();
		if(strpos($currentUrl, self::PDP_URL_ADD_CART) != false) {
			
		}		
		parent::updateQuoteItems($items);
	}
}
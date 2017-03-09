<?php
namespace PDP\Integration\Model\Pdpproduct;

use PDP\Integration\Api\PdpItemRepositoryInterface;
use PDP\Integration\Model\PdpproductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use PDP\Integration\Api\Data\PdpItemInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Checkout\Model\Cart as CustomerCart;

class PdpItemRepository implements PdpItemRepositoryInterface {

    /**
     * @var PdpproductFactory
     */
    protected $_pdpproductFactory;
	
    /**
     * @var \PDP\Integration\Api\Data\PdpReponseInterfaceFactory
     */
    protected $pdpReponseFactory;
	
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;	
	
    /**
     * @var DataObjectFactory
     */
    protected $objectFactory;
	
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;    
	
	/**
     * @var Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
	
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;	
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;	
	
    /**
     * @var PDP\Integration\Model\PdpquoteFactory
     */
    protected $_pdpquoteFactory;
	
    /**
     * PDP Integration session
     *
     * @var \PDP\Integration\Model\Session
     */
    protected $_pdpIntegrationSession;	

	/**
     * @param DataObjectFactory $objectFactory
     * @param Pdpproduct $pdpproduct
     * @param \PDP\Integration\Api\Data\PdpReponseInterfaceFactory $pdpReponseFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param ProductRepositoryInterface $productRepository
	 * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
	 * @param \PDP\Integration\Model\PdpquoteFactory $pdpquoteFactory
	 * @param CustomerCart $cart
	 * @param \PDP\Integration\Model\Session $pdpIntegrationSession
     * @param PdpproductFactory $pdpproductFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
		\PDP\Integration\Api\Data\PdpReponseInterfaceFactory $pdpReponseFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\UrlInterface $urlBuilder,
		ProductRepositoryInterface $productRepository,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		\PDP\Integration\Model\PdpquoteFactory $pdpquoteFactory,
		\PDP\Integration\Model\Session $pdpIntegrationSession,		
		CustomerCart $cart,		
        PdpproductFactory $pdpproductFactory
    ) {
        $this->objectFactory = $objectFactory;
		$this->pdpReponseFactory = $pdpReponseFactory;
		$this->storeManager = $storeManager;
		$this->urlBuilder = $urlBuilder;
		$this->productRepository = $productRepository;
		$this->_pdpOptions = $pdpOptions;
		$this->_pdpquoteFactory = $pdpquoteFactory;
		$this->_pdpIntegrationSession = $pdpIntegrationSession;		
		$this->cart = $cart;		
        $this->_pdpproductFactory = $pdpproductFactory;
    }
	
    /**
     * Perform persist operations for one entity
     *
     * @param PdpItemInterface $entity
     * @return \PDP\Integration\Api\Data\PdpReponseInterface
     */
    public function save(\PDP\Integration\Api\Data\PdpItemInterface $pdpItem)
    {
		$postDataArr = $pdpItem->__toArray();
		$reponse = $this->pdpReponseFactory->create();		
		if($this->_pdpOptions->statusPdpIntegration()) {
			if($pdpItem->getEntityId() && $pdpItem->getSku()) {
				$product = $this->productRepository->get($pdpItem->getSku());
				if($product->getTypeId()) {
					$dataOpt = array();
					if($pdpItem->getPdpOptions() != null) {
						$dataOpt['pdp_options'] = $postDataArr['pdp_options'];
					}
					if($pdpItem->getPdpPrintType() != null) {
						$dataOpt['pdp_print_type'] = $postDataArr['pdp_print_type'];
					}
					if(isset($dataOpt['pdp_options'])) {
						$pdp_option_data = $dataOpt['pdp_options'];
					}
					
					if(isset($dataOpt['pdp_print_type'])) {
						$pdp_print_type = $dataOpt['pdp_print_type'];
					}
					if(isset($pdp_option_data)) {
						$pdpOptSelect = $this->_pdpOptions->getOptionsSelect($pdp_option_data);
						$infoRequest = $this->_pdpOptions->getOptInfoRquest($pdpOptSelect);
						$additionalOptions = $this->_pdpOptions->getAdditionOption($pdpOptSelect);
						$dataOpt['pdp_options'] = $pdpOptSelect;
					} else {
						$additionalOptions = array();
						$infoRequest = array();
					}
					if(isset($pdp_print_type) && count($pdp_print_type)) {
						$printType = array('label' => 'Print type', 'value' => '');
						$printType['value'] = $pdp_print_type['title'];
						if(isset($pdp_print_type['price'])){
							$printTypePrice = $pdp_print_type['price'];
						}
						$printTypeValue = $pdp_print_type['value'];
						$additionalOptions[] = $printType;
						if(isset($printTypeValue)) {
							$infoRequest['pdp_print_type'] = $printTypeValue;
							if(isset($printTypePrice))
								$infoRequest['pdp_price'] += $printTypePrice;
						}
					}
					if($pdpItem->getQty()) {
						$infoRequest['qty'] = $pdpItem->getQty();
					}
					$infoRequest['product'] = $product->getEntityId();
					try {
						if($pdpItem->getItemId()) {
							$this->cart->removeItem($pdpItem->getItemId());
						}
						$product->addCustomOption('additional_options', serialize($additionalOptions));
						$this->cart->addProduct($product, $infoRequest);
						$this->cart->save();
						try{
							if (!$this->cart->getQuote()->getHasError()) {
								$modelPdpquote = $this->_pdpquoteFactory->create();
								$itembypro = $this->cart->getQuote()->getItemByProduct($product);
								if($itembypro != false) {
									$itemId = $itembypro->getItemId();
									$data = array(
										'item_id' => $itemId,
										'product_id' => $itembypro->getProductId(),
										'pdp_product_id' => $pdpItem->getEntityId(),
										'sku' => $pdpItem->getSku(),
										'store_id' => $itembypro->getStoreId(),
										'value' => serialize($dataOpt)
									);
									if($pdpItem->getDesignId()) {
										$data['design_id'] = $pdpItem->getDesignId();
									}
									if($pdpItem->getDesignUrl()) {
										$data['url'] = $pdpItem->getDesignUrl();
									}
									$dataItem = $modelPdpquote->_loadByItemId($itemId);
									if(count($dataItem)) {
										$data['pdpcart_id'] = $dataItem[0];
									}
									$modelPdpquote->addData($data);
									$modelPdpquote->save();									
								}
							}
						} catch(\Magento\Framework\Exception\LocalizedException $e) {
							$reponse->setStatus(false)
									->setMessage(nl2br($e->getMessage()));
							return $reponse;
						}
					} catch(\Magento\Framework\Exception\LocalizedException $e) {
						$reponse->setStatus(false)
								->setMessage(nl2br($e->getMessage()));
						return $reponse;
					}
					$this->_pdpIntegrationSession->setPdpCheckoutCart(1);
					$url = $this->urlBuilder->getUrl('checkout/cart');
					$reponse->setUrl($url)
							->setStatus(true)
							->setMessage('add product success');				
				} else {
					$reponse->setStatus(false)
							->setMessage('product add is not exists');
				}
			} else {
				$reponse->setStatus(false)
						->setMessage('post data failed');
			}			
		} else {
			$reponse->setStatus(false)
					->setMessage('post data failed, PDP Integration is not enable');
		}
		return $reponse;
	}	
}
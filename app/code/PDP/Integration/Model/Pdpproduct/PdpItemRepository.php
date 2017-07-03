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
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;	

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
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
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
        PdpproductFactory $pdpproductFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory
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
		$this->productFactory = $productFactory;
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
			if(($pdpItem->getEntityId() && $pdpItem->getSku()) || ($pdpItem->getSku() && $pdpItem->getCustomSize() != null)) {
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
					if($pdpItem->getProductColor() != null) {
						$productColor = $postDataArr['product_color'];
						$dataOpt['product_color'] = $productColor;
					}
					$additionalOptions = array();
					$infoRequest = array();
					if(isset($pdp_option_data)) {
						$_pdpOptSelect = $this->_pdpOptions->getOptionsSelect($pdp_option_data);
						$pdpOptSelect = $_pdpOptSelect['options'];
						$infoRequest = $this->_pdpOptions->getOptInfoRquest($pdpOptSelect);
						$additionalOptions = $this->_pdpOptions->getAdditionOption($pdpOptSelect);
						$dataOpt['pdp_options'] = $pdpOptSelect;
					}
					if($pdpItem->getDesignId()) {
						$infoRequest['design_id'] = $pdpItem->getDesignId();
						$additionalOptions[] = array('label' => 'Design Id', 'value' => 'pdp_design_id'.$pdpItem->getDesignId());
					}
					if($pdpItem->getCustomSize() != null) {
						$customSize = $pdpItem->getCustomSize();
						$unit = $customSize->getUnit();
						if($customSize->getHeight()) {
							$infoRequest['custom_size_height'] = $customSize->getHeight();
							$additionalOptions[] = array('label' => __('Height'), 'value' => $customSize->getHeight().$unit);
						}
						if($customSize->getWidth()) {
							$infoRequest['custom_size_width'] = $customSize->getWidth();
							$additionalOptions[] = array('label' => __('Width'), 'value' => $customSize->getWidth().$unit);
						}
						if($customSize->getSizeLayout()) {
							$infoRequest['custom_size_layout'] = $customSize->getSizeLayout();
							$additionalOptions[] = array('label' => __('Size layout'), 'value' => $customSize->getSizeLayout());
						}
					}
					if(isset($dataOpt['product_color']['color_price']) && $dataOpt['product_color']['color_price']) {
						$color_price = $dataOpt['product_color']['color_price'];
						if(isset($infoRequest['pdp_price'])) {
							$infoRequest['pdp_price'] += $color_price;
						} else {
							$infoRequest['pdp_price'] = $color_price;
						}
					}
					if(isset($pdp_print_type) && count($pdp_print_type)) {
						$printType = array('label' => __('Print type'), 'value' => '');
						if(isset($pdp_print_type['title'])) {
							$printType['value'] = $pdp_print_type['title'];
						}
						if(isset($pdp_print_type['price'])){
							$printTypePrice = $pdp_print_type['price'];
						}
						if(isset($pdp_print_type['value'])) {
							$printTypeValue = $pdp_print_type['value'];
						}
						if($printType['value'] != '') {
							$additionalOptions[] = $printType;
						}
						if(isset($printTypeValue)) {
							$infoRequest['pdp_print_type'] = $printTypeValue;
							if(isset($printTypePrice)) {
								if(isset($infoRequest['pdp_price'])) {
									$infoRequest['pdp_price'] += $printTypePrice;
								} else {
									$infoRequest['pdp_price'] = $printTypePrice;
								}
							}
						}
					}
					if(isset($productColor) && count($productColor)) {
						$product_color = array('label' => __('Color'), 'value' => __($productColor['color_name']));
						$additionalOptions[] = $product_color;
						if(isset($productColor['color_id'])) {
							$infoRequest['pdp_product_color'] = $productColor['color_id'];
						}
					}
					if($pdpItem->getQty()) {
						$infoRequest['qty'] = $pdpItem->getQty();
					}
					$infoRequest['product'] = $product->getEntityId();
					try {
						if($pdpItem->getItemId()) {
							$this->cart->removeItem($pdpItem->getItemId());
							if(isset($_pdpOptSelect) && $_pdpOptSelect['multiSize'] && count($_pdpOptSelect['multiSizeOpt']['values'])) {
								$multiSizeOpt = $_pdpOptSelect['multiSizeOpt'];
								foreach($multiSizeOpt['values'] as $_val) {
									$_product = $this->productFactory->create()->load($infoRequest['product']);
									$_infoRequest = $infoRequest;
									$_additionalOptions = $additionalOptions;
									$_infoRequest['qty'] = $_val['qty'];
									$_additionalOptions[] = array('label' => __($multiSizeOpt['title']), 'value' => __($_val['title']));
									$_infoRequest['pdp_options'][$multiSizeOpt['option_id']] = $_val['option_type_id'];
									$_infoRequest['pdp_price'] += $_val['price'];
									$_product->addCustomOption('additional_options', serialize($_additionalOptions));
									$this->cart->addProduct($_product, $_infoRequest);
								}
								$this->cart->save();
								$quoteItemsArr = $this->cart->getQuote()->getAllVisibleItems();
								foreach($quoteItemsArr as $__quoteItem) {
									try {
										if (!$this->cart->getQuote()->getHasError()) {
											$modelPdpquote = $this->_pdpquoteFactory->create();
											$itemId = $__quoteItem->getItemId();
											$dataItem = $modelPdpquote->loadByItemId($itemId);
											if(!$dataItem->getPdpcartId()) {
												$data = array(
													'item_id' => $itemId,
													'product_id' => $__quoteItem->getProductId(),
													'pdp_product_id' => $pdpItem->getEntityId(),
													'sku' => $pdpItem->getSku(),
													'store_id' => $__quoteItem->getStoreId(),
													'value' => serialize($dataOpt)
												);
												if($pdpItem->getDesignId()) {
													$data['design_id'] = $pdpItem->getDesignId();
												}
												if($pdpItem->getDesignUrl()) {
													$data['url'] = $pdpItem->getDesignUrl();
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
								}
							} else {
								$product->addCustomOption('additional_options', serialize($additionalOptions));
								$this->cart->addProduct($product, $infoRequest);
								$this->cart->save();
								try {
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
											$dataItem = $modelPdpquote->loadByItemId($itemId);
											if($dataItem->getPdpcartId()) {
												$data['pdpcart_id'] = $dataItem->getPdpcartId();
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
							}
						} else {
							if(isset($_pdpOptSelect) && $_pdpOptSelect['multiSize'] && count($_pdpOptSelect['multiSizeOpt']['values'])) {
								$multiSizeOpt = $_pdpOptSelect['multiSizeOpt'];
								foreach($multiSizeOpt['values'] as $_val) {
									$_product = $this->productFactory->create()->load($infoRequest['product']);
									$_infoRequest = $infoRequest;
									$_additionalOptions = $additionalOptions;
									$_infoRequest['qty'] = $_val['qty'];
									$_additionalOptions[] = array('label' => __($multiSizeOpt['title']), 'value' => __($_val['title']));
									$_infoRequest['pdp_options'][$multiSizeOpt['option_id']] = $_val['option_type_id'];
									$_infoRequest['pdp_price'] += $_val['price'];
									$_product->addCustomOption('additional_options', serialize($_additionalOptions));
									$this->cart->addProduct($_product, $_infoRequest);
								}
								$this->cart->save();
								$quoteItemsArr = $this->cart->getQuote()->getAllVisibleItems();
								foreach($quoteItemsArr as $__quoteItem) {
									try {
										if (!$this->cart->getQuote()->getHasError()) {
											$modelPdpquote = $this->_pdpquoteFactory->create();
											$itemId = $__quoteItem->getItemId();
											$dataItem = $modelPdpquote->loadByItemId($itemId);
											if(!$dataItem->getPdpcartId()) {
												$data = array(
													'item_id' => $itemId,
													'product_id' => $__quoteItem->getProductId(),
													'pdp_product_id' => $pdpItem->getEntityId(),
													'sku' => $pdpItem->getSku(),
													'store_id' => $__quoteItem->getStoreId(),
													'value' => serialize($dataOpt)
												);
												if($pdpItem->getDesignId()) {
													$data['design_id'] = $pdpItem->getDesignId();
												}
												if($pdpItem->getDesignUrl()) {
													$data['url'] = $pdpItem->getDesignUrl();
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
								}
							} else {
								$product->addCustomOption('additional_options', serialize($additionalOptions));
								$this->cart->addProduct($product, $infoRequest);
								$this->cart->save();
								try {
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
											$dataItem = $modelPdpquote->loadByItemId($itemId);
											if($dataItem->getPdpcartId()) {
												$data['pdpcart_id'] = $dataItem->getPdpcartId();
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
							}
						}
						//echo $this->cart->getQuote()->getId();die;
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
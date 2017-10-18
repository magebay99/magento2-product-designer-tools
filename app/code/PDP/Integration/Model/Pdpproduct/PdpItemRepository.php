<?php
namespace PDP\Integration\Model\Pdpproduct;

use PDP\Integration\Api\PdpItemRepositoryInterface;
use PDP\Integration\Helper\CorsResponseHelper;
use PDP\Integration\Model\PdpproductFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use PDP\Integration\Api\Data\PdpItemInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Checkout\Model\Cart as CustomerCart;
use PDP\Integration\Plugin\CorsHeadersPlugin;

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

    /** @var  \Magento\Framework\Webapi\Rest\Response */
    protected $_response;

    /**
     * CORS Response Helper . Add Headers to Response Object
     *
     * @var \PDP\Integration\Helper\CorsResponseHelper
     */
    private $_corsResponseHelper;


    /**
     * PdpItemRepository constructor.
     *
     * @param \Magento\Framework\DataObject\Factory                $objectFactory
     * @param \PDP\Integration\Api\Data\PdpReponseInterfaceFactory $pdpReponseFactory
     * @param \Magento\Store\Model\StoreManagerInterface           $storeManager
     * @param \Magento\Framework\UrlInterface                      $urlBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository
     * @param \PDP\Integration\Helper\PdpOptions                   $pdpOptions
     * @param \PDP\Integration\Model\PdpquoteFactory               $pdpquoteFactory
     * @param \PDP\Integration\Model\Session                       $pdpIntegrationSession
     * @param \Magento\Checkout\Model\Cart                         $cart
     * @param \PDP\Integration\Model\PdpproductFactory             $pdpproductFactory
     * @param \Magento\Catalog\Model\ProductFactory                $productFactory
     * @param \Magento\Framework\Webapi\Rest\Response              $response
     * @param \PDP\Integration\Helper\CorsResponseHelper           $corsResponseHelper
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
		\Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Webapi\Rest\Response $response,
        CorsResponseHelper $corsResponseHelper
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
		$this->_response = $response;
		$this->_corsResponseHelper = $corsResponseHelper;
    }
	
    /**
     * Perform persist operations for one entity
     *
     * @param PdpItemInterface $pdpItem
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
						$pdp_option_data = $dataOpt['pdp_options'];
					}
					if($pdpItem->getPdpPrintType() != null) {
						$dataOpt['pdp_print_type'] = $postDataArr['pdp_print_type'];
						$pdp_print_type = $dataOpt['pdp_print_type'];
						if(isset($dataOpt['pdp_print_type']['price_multi_size'])) {
							$__price_multi_size = array();
							foreach($dataOpt['pdp_print_type']['price_multi_size'] as $_val) {
								if(isset($_val['size']) && isset($_val['price'])) {
									if(isset($__price_multi_size[$_val['size']])) {
										$__price_multi_size[$_val['size']] += $_val['price'];
									} else {
										$__price_multi_size[$_val['size']] = $_val['price'];
									}
								}
							}
							$pdp_print_type['price_multi_size'] = $__price_multi_size;
						}
					}
					
					if($pdpItem->getProductColor() != null) {
						$productColor = $postDataArr['product_color'];
						$dataOpt['product_color'] = $productColor;
					}
					$usedNameNum = false;
					if($pdpItem->getMultiSize() != null) {
						//$dataOpt['multi_size'] = $postDataArr['multi_size'];
						$multi_size = array();
						$price_multi_size = array();
						foreach($postDataArr['multi_size'] as $sz_key => $sz_val) {
							$multi_size_item = array(
								//'name' => isset($sz_val['name']) ? $sz_val['name'] : '', 
								//'num' => isset($sz_val['num']) ? $sz_val['num'] : '',
								'qty' => isset($sz_val['qty']) ? $sz_val['qty'] : 1,
								'size' => isset($sz_val['size']) ? ucfirst($sz_val['size']) : '',
								'price' => isset($sz_val['price']) ? $sz_val['price'] : 0
							);
							if (isset($sz_val['name']) && isset($sz_val['num'])) {
								$usedNameNum = true;
								$multi_size_item['name'] = $sz_val['name'];
								$multi_size_item['num'] = $sz_val['num'];
							}
							if(isset($sz_val['size'])) {
								$multi_size[$sz_val['size']][] = $multi_size_item;
								if(!isset($price_multi_size[$sz_val['size']])) {
									if(isset($sz_val['price']) && $sz_val['price']) {
										$price_multi_size[$sz_val['size']] = $sz_val['price'];
									}
								}
							}
						}
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
						$pdpProductId = $product->getEntityId();
					} else {
						$pdpProductId = $pdpItem->getEntityId();
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
						if($pdpItem->getMultiSize() != null && !$usedNameNum) {
							if(isset($pdp_print_type['price_multi_size'])) {
								$printType = array('label' => __('Print type'), 'value' => '');
								if(isset($pdp_print_type['title']) && $pdp_print_type['title']) {
									$printType['value'] = $pdp_print_type['title'];
									$additionalOptions[] = $printType;
								}
							}
						} else {
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
							if($pdpItem->getMultiSize() != null) {
								if(count($multi_size)) {
									if ($usedNameNum) {
										$price_size = array();
										foreach($multi_size as $size_key => $item_multi_size) {
											foreach($item_multi_size as $_item_multi_size) {
												$infoRequest['pdp_price'] += $_item_multi_size['price'];
												break;
											}
										}
										$product->addCustomOption('additional_options', serialize($additionalOptions));
										$this->cart->addProduct($product, $infoRequest);
										$this->cart->save();
										try {
											if (!$this->cart->getQuote()->getHasError()) {
												$modelPdpquote = $this->_pdpquoteFactory->create();
												$itembypro = $this->cart->getQuote()->getItemByProduct($product);
												if($itembypro != false) {
													$_dataOpt = $dataOpt;
													$_dataOpt['multi_size'] = $postDataArr['multi_size'];
													$itemId = $itembypro->getItemId();
													$data = array(
														'item_id' => $itemId,
														'product_id' => $itembypro->getProductId(),
														'pdp_product_id' => $pdpProductId,
														'sku' => $pdpItem->getSku(),
														'store_id' => $itembypro->getStoreId(),
														'value' => serialize($_dataOpt)
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
									} else {
										foreach($multi_size as $mtize_key => $mtize_val){
											$_product = $this->productFactory->create()->load($infoRequest['product']);
											$_infoRequest = $infoRequest;
											$multi_size_qty = 0;
											if(count($mtize_val) > 1) {
												foreach($mtize_val as $mtize_val_val) {
													if(isset($mtize_val_val['qty'])) {
														$multi_size_qty = $multi_size_qty + $mtize_val_val['qty'];
													} else {
														$multi_size_qty = 1;
													}
												}
											} else {
												$multi_size_qty = isset($mtize_val[0]['qty'])?$mtize_val[0]['qty']:1;
											}
											$_infoRequest['qty'] = $multi_size_qty;
											$_infoRequest['size'] = $mtize_key;
											if(isset($pdp_print_type['price_multi_size']) && count($pdp_print_type['price_multi_size'])) {
												if(isset($pdp_print_type['price_multi_size'][$mtize_key])) {
													if(isset($_infoRequest['pdp_price'])) {
														$_infoRequest['pdp_price'] += $pdp_print_type['price_multi_size'][$mtize_key];
													} else {
														$_infoRequest['pdp_price'] = $pdp_print_type['price_multi_size'][$mtize_key];
													}
												}
											}
											if(isset($price_multi_size) && count($price_multi_size)) {
												if(isset($price_multi_size[$mtize_key])) {
													if(isset($_infoRequest['pdp_price'])) {
														$_infoRequest['pdp_price'] += $price_multi_size[$mtize_key];
													} else {
														$_infoRequest['pdp_price'] = $price_multi_size[$mtize_key];
													}
												}
											}
											$_additionalOptions = $additionalOptions;
											$_additionalOptions[] = array('label' => __('Size'), 'value' => ucfirst($mtize_val[0]['size']));
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
													if(!$dataItem->getPdpcartId() && $__quoteItem->getProductType() == \PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE) {
														$__infoRequest = $__quoteItem->getBuyRequest();
														/*$_dataOpt = $dataOpt;
														if(isset($__infoRequest['size'])) {
															$_val_size = $__infoRequest['size'];
															$_dataOpt['multi_size'] = isset($multi_size[$_val_size])?$multi_size[$_val_size]:'';
														}*/
														$data = array(
															'item_id' => $itemId,
															'product_id' => $__quoteItem->getProductId(),
															'pdp_product_id' => $pdpProductId,
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
												'pdp_product_id' => $pdpProductId,
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
							if($pdpItem->getMultiSize() != null) {
								if(count($multi_size)) {
									if ($usedNameNum) {
										foreach($multi_size as $item_multi_size) {
											foreach($item_multi_size as $_item_multi_size) {
												$infoRequest['pdp_price'] += $_item_multi_size['price'];
												break;
											}
										}										
										$product->addCustomOption('additional_options', serialize($additionalOptions));
										$this->cart->addProduct($product, $infoRequest);
										$this->cart->save();
										try {
											if (!$this->cart->getQuote()->getHasError()) {
												$modelPdpquote = $this->_pdpquoteFactory->create();
												$itembypro = $this->cart->getQuote()->getItemByProduct($product);
												if($itembypro != false) {
													$_dataOpt = $dataOpt;
													$_dataOpt['multi_size'] = $postDataArr['multi_size'];
													$itemId = $itembypro->getItemId();
													$data = array(
														'item_id' => $itemId,
														'product_id' => $itembypro->getProductId(),
														'pdp_product_id' => $pdpProductId,
														'sku' => $pdpItem->getSku(),
														'store_id' => $itembypro->getStoreId(),
														'value' => serialize($_dataOpt)
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
									} else {
										foreach($multi_size as $mtize_key => $mtize_val){
											$_product = $this->productFactory->create()->load($infoRequest['product']);
											$_infoRequest = $infoRequest;
											$multi_size_qty = 0;
											if(count($mtize_val) > 1) {
												foreach($mtize_val as $mtize_val_val) {
													if(isset($mtize_val_val['qty'])) {
														$multi_size_qty = $multi_size_qty + $mtize_val_val['qty'];
													} else {
														$multi_size_qty = 1;
													}
												}
											} else {
												$multi_size_qty = isset($mtize_val[0]['qty'])?$mtize_val[0]['qty']:1;
											}
											$_infoRequest['qty'] = $multi_size_qty;
											$_infoRequest['size'] = $mtize_key;
											if(isset($pdp_print_type['price_multi_size']) && count($pdp_print_type['price_multi_size'])) {
												if(isset($pdp_print_type['price_multi_size'][$mtize_key])) {
													if(isset($_infoRequest['pdp_price'])) {
														$_infoRequest['pdp_price'] += $pdp_print_type['price_multi_size'][$mtize_key];
													} else {
														$_infoRequest['pdp_price'] = $pdp_print_type['price_multi_size'][$mtize_key];
													}
												}
											}
											if(isset($price_multi_size) && count($price_multi_size)) {
												if(isset($price_multi_size[$mtize_key])) {
													if(isset($_infoRequest['pdp_price'])) {
														$_infoRequest['pdp_price'] += $price_multi_size[$mtize_key];
													} else {
														$_infoRequest['pdp_price'] = $price_multi_size[$mtize_key];
													}
												}
											}
											$_additionalOptions = $additionalOptions;
											$_additionalOptions[] = array('label' => __('Size'), 'value' => ucfirst($mtize_val[0]['size']));
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
													if(!$dataItem->getPdpcartId() && $__quoteItem->getProductType() == \PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE) {
														$__infoRequest = $__quoteItem->getBuyRequest();
														/*$_dataOpt = $dataOpt;
														if(isset($__infoRequest['size'])) {
															$_val_size = $__infoRequest['size'];
															$_dataOpt['multi_size'] = isset($multi_size[$_val_size])?$multi_size[$_val_size]:'';
														}*/
														$data = array(
															'item_id' => $itemId,
															'product_id' => $__quoteItem->getProductId(),
															'pdp_product_id' => $pdpProductId,
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
												'pdp_product_id' => $pdpProductId,
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
		$this->_response = $this->_corsResponseHelper->addCorsHeaders($this->_response);
		return $reponse;
	}
}
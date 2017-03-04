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
     * @var PDP\Integration\Model\PdpOrderFactory
     */
    protected $_pdpOrderFactory;	
	
	/**
     * @var PDP\Integration\Model\PdpOrderItemFactory
     */
    protected $_pdpOrderItemFactory;

	/**
     * @var PDP\Integration\Model\PdpOrderRelationFactory
     */
    protected $_pdpOrderRelationFactory;

	/**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
     */
    protected $countryInformation;

	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \PDP\Integration\Model\PdpOrderFactory $pdpOrderFactory
     * @param \PDP\Integration\Model\PdpOrderItemFactory $pdpOrderItemFactory
     * @param \PDP\Integration\Model\PdpOrderRelationFactory $pdpOrderRelationFactory
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     */
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\PDP\Integration\Model\PdpOrderFactory $pdpOrderFactory,
		\PDP\Integration\Model\PdpOrderItemFactory $pdpOrderItemFactory,
		\PDP\Integration\Model\PdpOrderRelationFactory $pdpOrderRelationFactory,
		\Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformation,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \PDP\Integration\Helper\PdpOptions $pdpOptions
    ) {
        $this->storeManager = $storeManager;
        $this->_pdpOrderFactory = $pdpOrderFactory;
        $this->_pdpOrderItemFactory = $pdpOrderItemFactory;
        $this->_pdpOrderRelationFactory = $pdpOrderRelationFactory;
        $this->countryInformation = $countryInformation;
        $this->_objectManager = $objectManager;
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
		$saveOrderInfoPdp = false;
		$items = $order->getAllVisibleItems();
		$pdpOrderTotal = 0;
		$_dataOrderItem = array();
		foreach($items as $item) {
			$quoteItemId = $item->getQuoteItemId();
			$_product = $item->getProduct();
			if($quoteItemId && $_product->getTypeId() == 'pdpro') {
				if($item->getRowTotal()) {
					$pdpOrderTotal += $item->getRowTotal();
				}
				$_pdpItemArr = $this->_pdpOptions->getPdpCartItem($quoteItemId);
				if(count($_pdpItemArr)) {
					$pdpOptions = array();
					$pdpItemArr = $_pdpItemArr[0];
					$saveOrderInfoPdp = true;
					$requestOptions = $item->getProductOptionByCode('info_buyRequest');
					$pdpValue = unserialize($pdpItemArr['value']);
					if(isset($pdpValue['pdp_options'])) {
						$pdpOptions = $pdpValue['pdp_options'];
					}
					if(isset($pdpValue['pdp_print_type'])) {
						$pdp_print_type = $pdpValue['pdp_print_type'];
					}
					if(count($pdpOptions)) {
						$pdpOptSelect = $this->_pdpOptions->getOptionsSelect($pdpOptions);
						$additional_options = $this->_pdpOptions->getAdditionOption($pdpOptSelect);
					} else {
						$additional_options = array();
					}
					$additionalOptions['info_buyRequest'] = $requestOptions;
					if(isset($pdp_print_type) && count($pdp_print_type)) {
						$printType = array('label' => 'Print type', 'value' => '');
						$printType['value'] = $pdp_print_type['title'];
						$printTypeValue = $pdp_print_type['value'];
						$additional_options[] = $printType;
					}
					if(count($additional_options)) {
						$additionalOptions['additional_options'] = $additional_options;
						$item->setProductOptions($additionalOptions);
						$item->save();
					}
					$itemPrice = 0;
					$dataOrderItem = array();
					if(isset($pdpItemArr['pdp_product_id'])) {
						$dataOrderItem['product_id'] = $pdpItemArr['pdp_product_id'];
						$pdpProductTypeModel = $this->_objectManager->get('PDP\Integration\Model\PdpProductType');
						$pdpProductType = $pdpProductTypeModel->load($dataOrderItem['product_id']);
					}
					if(isset($pdpItemArr['design_id'])) {
						$dataOrderItem['design_id'] = $pdpItemArr['design_id'];
					}
					if(isset($pdpItemArr['sku'])) {
						$dataOrderItem['sku'] = $pdpItemArr['sku'];
					}
					if($item->getQtyOrdered()) {
						$dataOrderItem['qty'] = $item->getQtyOrdered();
					}
					if(isset($pdpItemArr['value'])) {
						if(isset($pdpValue['pdp_options'])) {
							$itemQty = 1;
							if(isset($dataOrderItem['qty'])) {
								$itemQty = $dataOrderItem['qty'];
							}
							$pdpOptionsData = $this->_pdpOptions->prepareDataOptValue($pdpValue['pdp_options'], $itemQty);
							if(isset($pdpOptionsData['options'])) {
								$dataOrderItem['product_options'] = serialize(array('options' => $pdpOptionsData['options']));
							}
							if(isset($pdpOptionsData['price'])) {
								$itemPrice += $pdpOptionsData['price'];
							}
						}
						if(isset($pdpValue['pdp_print_type'])) {
							$pdp_print_type = $this->_pdpOptions->prepareDataPrintType($pdpValue['pdp_print_type']);
							if(isset($pdp_print_type['print_type'])) {
								$dataOrderItem['print_type'] = serialize($pdp_print_type['print_type']);
								if(isset($pdp_print_type['print_type']['cost'])) {
									$itemPrice += $pdp_print_type['print_type']['cost'];
								}
							}
						}
					}
					if(isset($pdpProductType)) {
						if($pdpProductType->getBasePrice()) {
							$dataOrderItem['base_price'] = $pdpProductType->getBasePrice();
							if($itemPrice) {
								$dataOrderItem['price'] = $pdpProductType->getBasePrice() + $itemPrice;
							} else {
								$dataOrderItem['price'] = $pdpProductType->getBasePrice();
							}
						}
						if($pdpProductType->getTitle()) {
							$dataOrderItem['name'] = $pdpProductType->getTitle();
						}
					}
					if(count($dataOrderItem)) {
						$_dataOrderItem[] = $dataOrderItem;
					}
				}
			}
		}
		$this->__saveInfoOrderPdp($order, $saveOrderInfoPdp, $_dataOrderItem, $pdpOrderTotal);
		return $this;
	}
	
	/**
	 * @param \Magento\Sales\Model\Order $order
	 * @param boolean $flag
	 * @param [] $_dataOrderItem
	 * @param float $pdpOrderTotal
	 * @return $this
	 */
	protected function __saveInfoOrderPdp(\Magento\Sales\Model\Order $order, $flag, $_dataOrderItem, $pdpOrderTotal) {
		if($flag) {
			$shippingAddress = $order->getShippingAddress();
			$orderId = $order->getEntityId();
			$billingAddress = $order->getBillingAddress();
			$dataOrder = array();
			if($order->getState()) {
				$dataOrder['status'] = $order->getState();
			}
			if($order->getSubtotal()) {
				//$dataOrder['subtotal'] = $order->getSubtotal();
				$dataOrder['subtotal'] = $pdpOrderTotal;
			}
			if($order->getShippingAmount()) {
				$dataOrder['shipping'] = $order->getShippingAmount();
			}
			if($order->getTaxAmount()) {
				$dataOrder['tax_amount'] = $order->getTaxAmount();
			}
			if($order->getGrandTotal()) {
				//$dataOrder['grandtotal'] = $order->getGrandTotal();
				$dataOrder['grandtotal'] = $pdpOrderTotal;
				if(isset($dataOrder['shipping'])) {
					$dataOrder['grandtotal'] += $dataOrder['shipping'];
				}
				if(isset($dataOrder['tax_amount'])) {
					$dataOrder['grandtotal'] += $dataOrder['tax_amount'];
				}
			}
			if($orderId) {
				$dataOrder['order_id'] = $orderId;
			}
			$_dataOrderItems = $_dataOrderItem;
			$this->saveInfoOrderPdp($billingAddress, $_dataOrderItems, $dataOrder);			
		}		
		return $this;
	}
	
	/**
	 * @param \Magento\Sales\Model\Order\Address $address
	 * @param [] $_dataOrderItems
	 * @param array $dataOrder
	 * @return $this
	 */
	protected function saveInfoOrderPdp(\Magento\Sales\Model\Order\Address $address, array $_dataOrderItems, array $dataOrder) {
		$modelPdporder = $this->_pdpOrderFactory->create();
		$dataPdpOrder = array();
		if($address->getName()) {
			$dataPdpOrder['name'] = $address->getName();
		}
		if($address->getEmail()) {
			$dataPdpOrder['email'] = $address->getEmail();
		}
		if($address->getRegion()) {
			$dataPdpOrder['state'] = $address->getRegion();
		}
		if($address->getTelephone()) {
			$dataPdpOrder['phone_number'] = $address->getTelephone();
		}
		if($address->getPostcode()) {
			$dataPdpOrder['zipcode'] = $address->getPostcode();
		}
		if($address->getStreet()) {
			$_address = $address->getStreet();
			if(isset($_address[0])) {
				$dataPdpOrder['address'] = $_address[0];
			}
			if(isset($_address[1])) {
				$dataPdpOrder['address2'] = $_address[1];
			}
		}
		if($address->getCountryId()) {
			$country = $this->countryInformation->getCountryInfo($address->getCountryId());
			$countryname = $country->getFullNameLocale();
			if($countryname) {
				$dataPdpOrder['country'] = $countryname;
			}
		}
		if(isset($dataOrder['status'])) {
			$dataPdpOrder['order_status'] = $dataOrder['status'];
		}
		if(isset($dataOrder['subtotal'])) {
			$dataPdpOrder['subtotal'] = $dataOrder['subtotal'];
		}
		if(isset($dataOrder['grandtotal'])) {
			$dataPdpOrder['grand_total'] = $dataOrder['grandtotal'];
		}
		if(isset($dataOrder['shipping'])) {
			$dataPdpOrder['shipping'] = $dataOrder['shipping'];
		}
		if(isset($dataOrder['tax_amount'])) {
			$dataPdpOrder['tax_amount'] = $dataOrder['tax_amount'];
		}
		if(count($dataPdpOrder)) {
			try{
				$modelPdporder->addData($dataPdpOrder)->save();
				$orderId = $modelPdporder->getOrderId();
			} catch(\Exception $e) {
				throw new \Magento\Framework\Exception\LocalizedException(
					new \Magento\Framework\Phrase($e->getMessage())
				);
			}
		}
		if(isset($dataOrder['order_id'])) {
			$mage_order_id = $dataOrder['order_id'];
		}
		if(isset($mage_order_id) && isset($orderId)) {
			$modelpdpOrderRelation = $this->_pdpOrderRelationFactory->create();
			$dataPdpOrderRelation = array('order_id' => $mage_order_id, 'pdp_order_id' => $orderId);
			try{
				$modelpdpOrderRelation->addData($dataPdpOrderRelation)->save();
			} catch(\Exception $e) {
				throw new \Magento\Framework\Exception\LocalizedException(
					new \Magento\Framework\Phrase($e->getMessage())
				);
			}
		}
		if(count($_dataOrderItems) && isset($orderId)) {
			$modelPdporderItem = array();
			$i = 0;
			foreach($_dataOrderItems as $dataOrderItem) {
				$modelPdporderItem[$i] = $this->_pdpOrderItemFactory->create();
				if($orderId) {
					$dataOrderItem['order_id'] = $orderId;
				}
				if(count($dataOrderItem)) {
					try{
						$modelPdporderItem[$i]->addData($dataOrderItem)->save();
					} catch(\Exception $e) {
						throw new \Magento\Framework\Exception\LocalizedException(
							new \Magento\Framework\Phrase($e->getMessage())
						);
					}
				}
				$i++;
			}
		}
		return $this;
	}
}
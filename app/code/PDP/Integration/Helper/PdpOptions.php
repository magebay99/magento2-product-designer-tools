<?php
namespace PDP\Integration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;

class PdpOptions extends AbstractHelper {
	
    const PATH_CONFIG_STATUS_PDP_INTEGRATION = 'pdpintegrat/general/enabled';
	
    const PATH_CONFIG_PATHPDP_TOOL = 'pdpintegrat/general/pathpdp';
	
    const PATH_CONFIG_BUTTON_CUSTOM_LABEL = 'pdpintegrat/general/labelbutton';
	
	/**
     * @var PdpquoteCollectionFactory
     */
    protected $pdpquoteCollectionFactory;
	
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
	
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;
	
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_pdpConfig;
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectmanager;
	
	protected $array_type_select;
	
	
    /**
     * 
	 * @param Context $context
	 * @param PriceCurrencyInterface $priceCurrency
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\View\Page\Config $pdpConfig
	 * @param \Magento\Framework\ObjectManagerInterface $objectmanager
	 * @param \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
     */	
	public function __construct(
		Context $context,
		PriceCurrencyInterface $priceCurrency,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Page\Config $pdpConfig,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
		\PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
	) {
		parent::__construct($context);
		$this->priceCurrency = $priceCurrency;
		$this->_storeManager = $storeManager;
		$this->_pdpConfig = $pdpConfig;
		$this->_objectmanager = $objectmanager;
		$this->pdpquoteCollectionFactory = $pdpquoteCollectionFactory;
		$this->array_type_select = array('drop_down','radio','checkbox','multiple','hidden');
	}

    /**
     * Get quote store model object
     *
     * @return  \Magento\Store\Model\Store
     */
    public function getStore()
    {
		$storeId = $this->_storeManager->getStore()->getId();
        return $this->_storeManager->getStore($storeId);
    }

	/**
	 * @param string $sku
	 * @return boolean
	 */
	public function checkProductIsPdp($sku) {
		if(strpos($sku, 'PDP-') != false) {
			return true;
		}
		return false;
	}
	
    /**
     * Retrieve true if PDP Integration is enabled
     * @return boolean
     */
    public function statusPdpIntegration()
    {
        return (bool) $this->scopeConfig->getValue(
            self::PATH_CONFIG_STATUS_PDP_INTEGRATION,
            ScopeInterface::SCOPE_STORE
        );
    }
	
	/**
	 * @param int $itemId
	 * @return String
	 */
	public function getLinkDesignPdpWithItemId($itemId) {
		$url = '';
		if($itemId) {
			$pdpCartarr = $this->getPdpCartItem($itemId);
			if(count($pdpCartarr)) {
				$url = $pdpCartarr[0]['url'].'&itemid='.$itemId;
			}
		}
		return $url;
	}
	
	/**
	 * @param array $newOptions
	 * @param array $prevOptions
	 * @return array
	 */
    public function comparisonArrays($newOptions, $prevOptions) {
        $diffOptions = array();
        foreach ($newOptions as $key=>$op) {
            if (isset($prevOptions[$key])) {
                if (is_array($op)) {
                    $result = $this->comparisonArrays($op, $prevOptions[$key]);
                    if ($result) $diffOptions[$key] = $result;
                } else {
                    if ($prevOptions[$key]!=$op) $diffOptions[$key] = $op;
                }
            } else {
                $diffOptions[$key] = $op;
            }
        }
        return $diffOptions;
    }
	
	/**
	 * @param string $sku
	 * @return String
	 */
	public function getLinkDesignPdp($sku) {
		$url = '#';
		if($this->statusPdpIntegration()) {
			$pos = strpos($sku,'PDP');
			$proId = '';
			if( $pos === false ) {
				$pdpProdu = $this->_objectmanager->get('PDP\Integration\Model\PdpProductType')->getProductWithSku($sku);
				foreach($pdpProdu as $key => $value) {
					if(isset($value['type_id'])) {
						$proId = $value['type_id'];
					}
				}
			} else {
				$_sku = explode('PDP-',$sku);
				$pdpProdu = $this->_objectmanager->get('PDP\Integration\Model\PdpProductType')->getProductWithSku($_sku[1]);
				foreach($pdpProdu as $key => $value) {
					if(isset($value['type_id'])) {
						$proId = $value['type_id'];
					}
				}				
			}
			if($proId) {
				$url = $this->getUrlToolDesign().'/?pid='.$proId;
			} elseif( strpos($sku,'CustomDismensionProd') !== false ) {
				$url = $this->getUrlToolDesign().'/?width=594&height=420&unit=mm&size_layout=Landscape&custom=1';
			}
		}
		return $url;
	}
	
    /**
     * Retrieve url PDP tool design
     * @return String
     */
	public function getUrlToolDesign() {
        $url = $this->scopeConfig->getValue(
            self::PATH_CONFIG_PATHPDP_TOOL,
            ScopeInterface::SCOPE_STORE
        );
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$baseUrl = $this->_urlBuilder->getBaseUrl();
			return $baseUrl.$url;
		} else {
			return $url;
		}
	}
	
	/**
	 * Retrieve label button design
	 * @return string
	 */
	public function getLabelCustom() {
        return $this->scopeConfig->getValue(
            self::PATH_CONFIG_BUTTON_CUSTOM_LABEL,
            ScopeInterface::SCOPE_STORE
        );
	}
	
    /**
     * Get item price converted to item currency
	 * @param float $price
	 * @param int $storeId
     * @return float
     */
    public function getConvertedPrice($price, $storeId)
    {
		$_price = $this->priceCurrency->convert($price, $storeId);
        return $_price;
    }
	
    /**
     * @param float $price
     * @param int $storeId
     * @return string
     */
    public function formatPrice($price, $storeId)
    {
        return $this->priceCurrency->format(
            $price,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $storeId
        );
    }
	
	/**
     * @param array $options
	 *
	 * @return array()
     */		
    public function getOptInfoRquest(array $options) {
		$infoRequest = array(
			'pdp_options' => array(),
			'pdp_price' => 0
		);
		$pdpPrice = 0;
		foreach($options as $key => $val) {
			$optId = $val['option_id'];
			if(in_array($val['type'],$this->array_type_select)) {
				$qty_input = false;
				$value = array();
				if($val['qnty_input']) {
					$qty_input = true;
				}
				foreach($val['values'] as $_key => $_val) {
					if(intval($_val['checked']) && $_val['selected'] && !$_val['disabled']) {
						$value[] = $_val['option_type_id'];
						if(intval($_val['qty']) > 1 && $qty_input) {
							$pdpPrice = $pdpPrice + floatval($_val['price'])*intval($_val['qty']);
						} else {
							$pdpPrice += floatval($_val['price']);
						}
					}
				}
				$infoRequest['pdp_options'][$optId] = implode(",", $value);
			} elseif($val['type'] == 'field' || $val['type'] == 'area') {
				if($val['default_text']) {
					$infoRequest['pdp_options'][$optId] = $val['default_text'];
					$pdpPrice += floatval($val['price']);
				}
			} elseif($val['type'] == 'file') {
				
			}
			$infoRequest['pdp_price'] = $pdpPrice;
		}
		return $infoRequest;
	}
	
	/**
     * @param array $options
	 *
	 * @return array()
     */	
	public function getAdditionOption(array $options) {
		$additionalOptions = array();
		foreach($options as $key => $val) {
			$item = array(
				'label' => __($val['title']),
				'value' => ''
			);
			if(in_array($val['type'],$this->array_type_select)) {
				$value = array();
				foreach($val['values'] as $_key => $_val) {
					if(intval($_val['checked']) && $_val['selected'] && !$_val['disabled']) {
						$value[] = __($_val['title']);
					}
				}
				$item['value'] = implode(",", $value);
			} elseif($val['type'] == 'field' || $val['type'] == 'area') {
				if($val['default_text']) {
					$item['value'] = $val['default_text'];
				}
			} elseif($val['type'] == 'file') {
				
			}
			$additionalOptions[] = $item;
		}
		return $additionalOptions;
	}
	
	/**
	 * @param array $value
	 * @return bolean
	 */
	protected function checkMultipleSize(array $value) {
		return false;
		if(isset($value['qnty_input']) && $value['qnty_input']) {
			if(isset($value['type']) && $value['type'] == 'checkbox') {
				return true;
			}
		}
		return false;
	}
	
	/**
     * @param array $options
	 *
	 * @return array()
     */		
	public function getOptionsSelect(array $options) {
		$_result = array('multiSize'=>false, 'multiSizeOpt' => array(), 'options' => array());
		$result = array();
		$_key = 0;
		foreach($options as $key => $val) {
			if(!$val['disabled']) {
				if(in_array($val['type'],$this->array_type_select)) {
					$_result['multiSize'] = $this->checkMultipleSize($val);
					$flag=false;
					$optVal = array();
					foreach($val['values'] as $opt_key => $opt_val) {
						if(intval($opt_val['checked']) && $opt_val['selected'] && !$opt_val['disabled']) {
							$optVal[] = $opt_val;
							$flag = true;
						}
					}
					if($flag) {
						if($_result['multiSize']) {
							$_result['multiSizeOpt'] = $val;
							$_result['multiSizeOpt']['values'] = $optVal;
						} else {
							$result[$_key] = $val;
							$result[$_key]['values'] = $optVal;
							$_key++;
						}
					} else {
						$_result['multiSize'] = false;
					}
				} elseif($val['type'] == 'field' || $val['type'] == 'area') {
					if($val['default_text']) {
						$result[$_key] = $val;
						$_key++;
					}
				} elseif($val['type'] == 'file') {
					
				}
			}
		}
		$_result['options'] = $result;
		return $_result;
	}
	
    /**
     * @param int $itemId
	 *
	 * @return array()
     */		
	public function getPdpCartItem($itemId) {
		$optValue = array();
		if($itemId) {
			$itemData = $this->__getPdpCartItem($itemId);
			$optValue = $itemData->getData();
		}
		return $optValue;
	}
	
    /**
     * @param int $itemId
	 *
	 * @return \PDP\Integration\Model\Pdpquote | null
     */	
	protected function __getPdpCartItem($itemId) {
		$itemData = null;
		if($itemId) {
			$itemData = $this->pdpquoteCollectionFactory->create()
				 ->addFieldToFilter('item_id', array('eq' => $itemId));
		}
		return $itemData;
	}
	
	/**
	 * @param array $options
	 * @param int $itemQty
	 * @return array
	 */
	public function prepareDataOptValue(array $options, $itemQty) {
		$result = array();
		$storeId = $this->_storeManager->getStore()->getId();
		$result['options'] = array();
		$result['price'] = 0;
		foreach($options as $key => $val) {
			$item = array();
			$item['option_id'] = $val['option_id'];
			$item['option_type'] = $val['type'];
			if(in_array($val['type'], $this->array_type_select)) {
				$item['label'] = $val['title'];
				if($val['type'] != 'checkbox' && $val['type'] != 'multiple') {
					foreach($val['values'] as $_key => $_val) {
						if(intval($_val['checked']) && $_val['selected'] && !$_val['disabled']) {
							if($_val['price']) {
								$item['value'] = $_val['qty'] . ' x ' .$_val['title']. ' - '.$this->getConvertedPrice($_val['price'], $storeId);
								$result['price'] = $result['price'] + $_val['qty']*$_val['price'];
							} else {
								$item['value'] = $_val['qty'] . ' x ' .$_val['title'];
							}
							$item['option_value'] = $_val['option_type_id'];
							break;
						}
					}
				} elseif($val['type'] == 'checkbox' || $val['type'] == 'multiple') {
					$itemValue = array();
					$itemOptionValue = array();
					foreach($val['values'] as $_key => $_val) {
						$__qty = $__val['qty']*$itemQty;
						if(intval($_val['checked']) && $_val['selected'] && !$_val['disabled']) {
							if($_val['price']) {
								$itemValue[] = $__qty.' x '.$_val['title'].' - '.$this->getConvertedPrice($_val['price'], $storeId);
								$result['price'] = $result['price'] + $_val['qty']*$_val['price'];
							} else {
								$itemValue[] = $__qty.' x '.$_val['title'];
							}
							$itemOptionValue[] = $_val['option_type_id'];
						}
					}
					$item['value'] = implode(',&nbsp', $itemValue);
					$item['option_value'] = implode(',', $itemOptionValue);
				}
			} elseif($val['type'] == 'field' || $val['type'] == 'area') {
				if($val['price']) {
					$item['label'] = $itemQty .' x '.$val['title'].' - '.$this->getConvertedPrice($val['price'], $storeId);
					$result['price'] = $result['price'] + $val['price'];
				} else {
					$item['label'] = $val['title'];
				}
				$item['value'] = $val['default_text'];
				$item['option_value'] = $val['default_text'];
			} elseif($val['type'] == 'file') {
				
			}
			$result['options'][] = $item;
		}
		return $result;
	}
	
	/**
	 * @param array $printType
	 * @return array
	 */
	public function prepareDataPrintType(array $printType) {
		$result = array(
			'print_type' => array()
		);
		if(isset($printType['title'])) {
			$result['print_type']['name'] = $printType['title'];
		}
		if(isset($printType['value'])) {
			$result['print_type']['id'] = $printType['value'];
		}
		if(isset($printType['price'])) {
			$result['print_type']['cost'] = $printType['price'];
		}
		return $result;
	}
}
<?php
namespace PDP\Integration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;

class PdpOptions extends AbstractHelper {
	
    const PATH_CONFIG_STATUS_PDP_INTEGRATION = 'pdpintegrat/general/enabled';
	
    const PATH_CONFIG_PATHPDP_TOOL = 'pdpintegrat/general/pathpdp';
	
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
			}
		}
		return $url;
	}
	
    /**
     * Retrieve url PDP tool design
     * @return String
     */
	public function getUrlToolDesign() {
        return $this->scopeConfig->getValue(
            self::PATH_CONFIG_PATHPDP_TOOL,
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
    public function getOptInfoRquest($options) {
		$infoRequest = array(
			'pdp_options' => array(),
			'pdp_price' => 0
		);
		$pdpPrice = 0;
		$array_type_select = array('drop_down','radio','checkbox','multiple','hidden');
		foreach($options as $key => $val) {
			$optId = $val['option_id'];
			if(in_array($val['type'],$array_type_select)) {
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
	public function getAdditionOption($options) {
		$array_type_select = array('drop_down','radio','checkbox','multiple','hidden');
		$additionalOptions = array();
		foreach($options as $key => $val) {
			$item = array(
				'label' => $val['title'],
				'value' => ''
			);
			if(in_array($val['type'],$array_type_select)) {
				$value = array();
				foreach($val['values'] as $_key => $_val) {
					if(intval($_val['checked']) && $_val['selected'] && !$_val['disabled']) {
						$value[] = $_val['title'];
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
    * @param array $options
	*
	* @return array()
    */		
	public function getOptionsSelect($options) {
		$result = array();
		$array_type_select = array('drop_down','radio','checkbox','multiple','hidden');
		foreach($options as $key => $val) {
			if(!$val['disabled']) {
				if(in_array($val['type'],$array_type_select)) {
					$flag=false;
					foreach($val['values'] as $opt_key => $opt_val) {
						if(intval($opt_val['checked']) && $opt_val['selected'] && !$opt_val['disabled']) {
							$flag = true;
						}
					}
					if($flag) {
						$result[] = $val;
					}
				} elseif($val['type'] == 'field' || $val['type'] == 'area') {
					if($val['default_text']) {
						$result[] = $val;
					}
				} elseif($val['type'] == 'file') {
					
				}
			}
		}
		return $result;
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
}
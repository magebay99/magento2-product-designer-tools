<?php
namespace PDP\Integration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Data extends AbstractHelper {

	const PATH_CONFIG_PATHPDP_TOOL = 'pdpintegrat/general/pathpdp';
	
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
	
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_pdpConfig;
	
	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $_objectmanager;
	
    /**
     * @var \PDP\Integration\Model\PdpGuestDesignFactory
     */
    private $_pdpGuestDesignFactory;
	
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;	

    /**
	 * @param Context $context
	 * @param PriceCurrencyInterface $priceCurrency
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\View\Page\Config $pdpConfig
	 * @param \PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory
	 * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     */	
	public function __construct(
		Context $context,
		PriceCurrencyInterface $priceCurrency,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Page\Config $pdpConfig,
		\PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory,
		\Magento\Framework\ObjectManagerInterface $objectmanager
	) {
		parent::__construct($context);
		$this->priceCurrency = $priceCurrency;
		$this->_storeManager = $storeManager;
		$this->_pdpConfig = $pdpConfig;
		$this->_pdpGuestDesignFactory = $pdpGuestDesignFactory;
		$this->_objectmanager = $objectmanager;
	}
	
	/**
	 * @param int $customerId
	 * @return array
	 */
	public function getProductCustomDesignWithCustomerId($customerId) {
		$dataGuestDesign = $this->_pdpGuestDesignFactory->create()->loadByCustomerId($customerId);
		$itemValue = unserialize($dataGuestDesign->getItemValue());
		return $itemValue;
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
     * Retrieve url PDP tool design
     * @return String
     */
	public function getUrlToolDesign() {
        return $this->scopeConfig->getValue(
            self::PATH_CONFIG_PATHPDP_TOOL,
            ScopeInterface::SCOPE_STORE
        );
	}	
}
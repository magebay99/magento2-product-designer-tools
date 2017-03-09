<?php
namespace PDP\Integration\Model;

/**
 * PDP Integration session model
 */
class Session extends \Magento\Framework\Session\SessionManager
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
	
    /**
     * Pdp Design instance
     *
     * @var PdpDesign
     */
    protected $_pdpDesign;
	
    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->_remoteAddress = $remoteAddress;
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
    }
	
    /**
     * @return string
     * @codeCoverageIgnore
     */
    protected function _getPdpDesignIdKey()
    {
        return 'pdp_design_id_' . $this->_storeManager->getStore()->getWebsiteId();
    }
	
    /**
     * @param int $pdpDesignId
     * @return void
     * @codeCoverageIgnore
     */
    public function setPdpDesignId($pdpDesignId)
    {
        $this->storage->setData($this->_getPdpDesignIdKey(), $pdpDesignId);
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getPdpDesignId()
    {
        return $this->getData($this->_getPdpDesignIdKey());
    }

    /**
     * Destroy/end a session
     * Unset all data associated with object
     *
     * @return $this
     */
    public function clearPdpDesign()
    {
		$this->setPdpDesignId(null);
		$this->_pdpDesign = null;
		return $this;
	}
	
    /**
     * Unset all session data and pdpDesign
     *
     * @return $this
     */
    public function clearStorage()
    {
        parent::clearStorage();
		$this->_pdpDesign = null;
        return $this;
    }	
}
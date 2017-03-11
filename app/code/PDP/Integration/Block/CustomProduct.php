<?php
namespace PDP\Integration\Block;

use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class CustomProduct extends \PDP\Integration\Block\AbstractPdpAcc {

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;
	
    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;
	
    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;

    /**
     * @var \PDP\Integration\Helper\Data
     */
    private $_pdpHelper;
	
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
	
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
	
	/**
	 * @var array
	 */
    protected $guestDesign;	
	
    /**
     * @param \PDP\Integration\Block\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param ProductRepositoryInterface $productRepository,
	 * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \PDP\Integration\Helper\Data $pdpHelper
     * @param array $data
     */
    public function __construct(
        \PDP\Integration\Block\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		ProductRepositoryInterface $productRepository,
		\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
		\PDP\Integration\Helper\Data $pdpHelper,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
		$this->productRepository = $productRepository;
		$this->priceCurrency = $priceCurrency;
        $this->_pdpHelper = $pdpHelper;
        parent::__construct($context, $data);
    }
	
    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                '*'
            )->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
	}
	
	/**
	 * @return array
	 */
	public function getProductGuestDesign() {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
		if(!$this->guestDesign) {
			$this->guestDesign = $this->_pdpHelper->getProductCustomDesignWithCustomerId($customerId);
		}
		if(!$this->guestDesign) {
			$this->guestDesign = array();
		}
		return $this->guestDesign;
	}
	/**
	 * @param int $id
	 * @return \Magento\Catalog\Model\Product
	 */
	public function getProductWithId($id) {
		$product = $this->productRepository->getById($id);
		return $product;
	}
	
	/**
	 * @param \Magento\Catalog\Model\Product $product
	 * @return string
	 */
	public function getImageProduct(\Magento\Catalog\Model\Product $product) {
		$imageUrl = '';
		if($product->getEntityId()) {
			$helperImage = $this->_objectManager->get('Magento\Catalog\Helper\Image');
			$imageUrl = $helperImage->init($product, 'product_page_image_medium')
				->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
				->setImageFile($product->getFile())
				->resize(207)
				->getUrl();
		}
		return $imageUrl;
	}
	
	/**
	 * @param \Magento\Catalog\Model\Product $product
	 * @return string
	 */	
	public function getPriceHtml(\Magento\Catalog\Model\Product $product) {
		$storeId = $this->_storeManager->getStore()->getId();
		$price = $this->_pdpHelper->getConvertedPrice($product->getFinalPrice(), $storeId);
		return $this->_pdpHelper->formatPrice($price, $storeId);
	}
	
	/**
	 * @param int $id
	 * @return array
	 */
	public function getDesignWithId($id) {
		$sideThubms = array();
		$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->load($id);
		if($pdpDesignJson->getDesignId()) {
			$sideThubms = unserialize($pdpDesignJson->getSideThumb());
		}
		return $sideThubms;
	}
	
	/**
	 * @return string
	 */
	public function getUrlToolDesign() {
		return $this->_pdpHelper->getUrlToolDesign();
	}
	
	/**
	 * @param int $designId
	 * @return string
	 */
	public function getLinkZipDesign($designId) {
		$url = $this->_pdpHelper->getUrlToolDesign();
		$param = '';
		if($designId) {
			$param = 'rest/design-download?id='.$designId.'&zip=1';
		} else {
			return '#';
		}
		if(substr($url, -1) == '/') {
			$url .= $param;
		} else {
			$url .= '/'.$param;
		}
		return $url;
	}
	
	/**
	 * @param int $designId
	 * @param int $productId
	 * @return string
	 */
	public function getLinkEdit($designId, $productId) {
		$url = $this->_pdpHelper->getUrlToolDesign();
		$param = '';
		if($productId) {
			$param .= '?pid='.$productId;
		}
		if($designId) {
			$param .= '&tid='.$designId;
		}
		
		if(substr($url, -1) == '/') {
			$url .= $param;
		} else {
			$url .= '/'.$param;
		}
		return $url;
	}
	
    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = $this->_objectManager->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }
	
    /**
     * Retrieve rendered item html content
     *
     * @param \Magento\Framework\DataObject $item
     * @return string
     */
    public function getItemHtml(\Magento\Framework\DataObject $item, $status)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        return $this->getItemRenderer($type)->setItem($item)->setStatus($status)->toHtml();
	}
	
    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        /** @var $renderer \Magento\Sales\Block\Adminhtml\Items\AbstractItems */
        $renderer = $this->getChildBlock($type) ?: $this->getChildBlock('default');
        if (!$renderer instanceof \Magento\Framework\View\Element\BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        $renderer->setColumnRenders($this->getLayout()->getGroupChildNames($this->getNameInLayout(), 'column'));

        return $renderer;
    }
}
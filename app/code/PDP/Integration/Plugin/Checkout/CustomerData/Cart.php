<?php

namespace PDP\Integration\Plugin\Checkout\CustomerData;

class Cart
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
	
    /**
     * @var \PDP\Integration\Block\Item\Price\Renderer
     */
    protected $itemPriceRenderer;

    /**
     * @var \Magento\Quote\Model\Quote|null
     */
    protected $quote = null;

    /**
     * @var array|null
     */
    protected $totals = null;	
	
    /**
     * @var PdpquoteCollectionFactory
     */
    protected $pdpquoteCollectionFactory;	
	
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** 
	 * @var PDP\Integration\Helper\PdpOptions 
	 */
    private $_helperPdpOpt;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
	 * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory
     * @param \PDP\Integration\Block\Item\Price\Renderer $itemPriceRenderer
	 * @param \PDP\Integration\Helper\PdpOptions $helperPdpOpt
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
		\Magento\Framework\Json\Helper\Data $jsonHelper,
		\PDP\Integration\Model\ResourceModel\Pdpquote\CollectionFactory $pdpquoteCollectionFactory,
        \PDP\Integration\Block\Item\Price\Renderer $itemPriceRenderer,
		\PDP\Integration\Helper\PdpOptions $helperPdpOpt,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->checkoutHelper = $checkoutHelper;
		$this->jsonHelper = $jsonHelper;
		$this->pdpquoteCollectionFactory = $pdpquoteCollectionFactory;
        $this->itemPriceRenderer = $itemPriceRenderer;
		$this->_helperPdpOpt = $helperPdpOpt;
		$this->storeManager = $storeManager;
    }	
	
    /**
     * @param int $itemId
	 *
	 * @return \PDP\Integration\Model\Pdpquote | null
     */	
	protected function getPdpCartItem($itemId) {
		$itemData = null;
		if($itemId) {
			$itemData = $this->pdpquoteCollectionFactory->create()
				 ->addFieldToFilter('item_id', array('eq' => $itemId));
		}
		return $itemData;
	}
	
    /**
     * Custom data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        $items =$this->getQuote()->getAllVisibleItems();
        if (is_array($result['items'])) {
            foreach ($result['items'] as $key => $itemAsArray) {
				$itemId = $itemAsArray['item_id'];
                if ($item = $this->findItemById($itemId, $items)) {
					$pdpCartItem = $this->getPdpCartItem($itemId);
					if($pdpCartItem != null && count($pdpCartItem->getData()) && $result['items'][$key]['product_type'] == 'pdpro') {
						//$sku = $result['items'][$key]['product_sku'];
						$url = $this->_helperPdpOpt->getLinkDesignPdpWithItemId($itemId);
						$result['items'][$key]['configure_url'] = $url;
					}
                }
            }
        }
        return $result;
    }
	
    /**
     * Get subtotal, including tax
     *
     * @return float
     */
    protected function getSubtotalInclTax()
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            $subtotal = $totals['subtotal']->getValueInclTax() ?: $totals['subtotal']->getValue();
        }
        return $subtotal;
    }

    /**
     * Get subtotal, excluding tax
     *
     * @return float
     */
    protected function getSubtotalExclTax()
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            $subtotal = $totals['subtotal']->getValueExclTax() ?: $totals['subtotal']->getValue();
        }
        return $subtotal;
    }

    /**
     * Get totals
     *
     * @return array
     */
    public function getTotals()
    {
        // TODO: TODO: MAGETWO-34824 duplicate \Magento\Checkout\CustomerData\Cart::getSectionData
        if (empty($this->totals)) {
            $this->totals = $this->getQuote()->getTotals();
        }
        return $this->totals;
    }	
	
    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }
	
    /**
     * Find item by id in items haystack
     *
     * @param int $id
     * @param array $itemsHaystack
     * @return \Magento\Quote\Model\Quote\Item | bool
     */
    protected function findItemById($id, $itemsHaystack)
    {
        if (is_array($itemsHaystack)) {
            foreach ($itemsHaystack as $item) {
                /** @var $item \Magento\Quote\Model\Quote\Item */
                if ((int)$item->getItemId() == $id) {
                    return $item;
                }
            }
        }
        return false;
    }	
}
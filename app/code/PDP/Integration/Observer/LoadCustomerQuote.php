<?php
namespace PDP\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadCustomerQuote implements ObserverInterface {
	
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
	
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
	
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;
	
    /**
     * @var PdpquoteFactory
     */
    private $pdpquoteFactory;
	
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\Registry $registry
    ) {
        $this->_customerSession = $customerSession;
        $this->messageManager = $messageManager;
		$this->registry = $registry;
    }
	
    /**
     * Customer load quote action
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return $this;
        }
		try {
			$checkoutSession = $observer->getCheckoutSession();
			$customerQuote = $checkoutSession->getQuote();
			$quoteItems = $customerQuote->getAllVisibleItems();
			$dataObject = new \Magento\Framework\DataObject();
			$dataItems = array();
			foreach($quoteItems as $item) {
				$itemId = $item->getItemId();
				$modelPdpquote = $this->getPdpQuoteFactory()->create();
				$dataItem = $modelPdpquote->loadByItemId($itemId);
				if($dataItem->getPdpcartId()) {
					$dataItem = array(
						'product_id' => $dataItem->getProductId(),
						'pdp_product_id' => $dataItem->getPdpProductId(),
						'design_id' => $dataItem->getDesignId(),
						'url' => $dataItem->getUrl(),
						'sku' => $dataItem->getSku(),
						'value' => unserialize($dataItem->getValue())
					);
					array_push($dataItems, $dataItem);
				}
			}
			if(count($dataItems)) {
				$dataObject->setPdpQuoteItem(serialize($dataItems));
				$this->registry->register('pdp_quote_item', $dataObject);
			}
		} catch(\Exception $e) {
			$this->messageManager->addException($e, __('Load guest design error'));
		}
	}
	
    /**
     * @return QuoteRepository
     * @deprecated
     */
    private function getQuoteRepository()
    {
        if (!$this->quoteRepository) {
            $this->quoteRepository = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Quote\Api\CartRepositoryInterface::class);
        }
        return $this->quoteRepository;
    }
	
	/**
	 * @return PdpQuote
	 */
	private function getPdpQuoteFactory() {
        if (!$this->pdpquoteFactory) {
            $this->pdpquoteFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(\PDP\Integration\Model\PdpquoteFactory::class);
        }
        return $this->pdpquoteFactory;
	}
}
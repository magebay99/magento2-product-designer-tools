<?php
namespace PDP\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;

class LoadGuestDesign implements ObserverInterface {

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
	
    /**
     * PDP Integration session
     *
     * @var \PDP\Integration\Model\Session
     */
    protected $_pdpIntegrationSession;
	
    /**
     * @var \PDP\Integration\Model\PdpGuestDesignFactory
     */
    protected $_pdpGuestDesignFactory;
	
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var PdpquoteFactory
     */
    private $pdpquoteFactory;

    /**
     * @var PdpProductType
     */
    private $pdpProductType;
	
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \PDP\Integration\Model\Session $pdpIntegrationSession
     * @param \PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory
     * @param \Magento\Framework\Registry $registry
	 * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
		\PDP\Integration\Model\Session $pdpIntegrationSession,
		\PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory,
		\Magento\Framework\Registry $registry,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_customerSession = $customerSession;
		$this->_pdpIntegrationSession = $pdpIntegrationSession;
		$this->_pdpGuestDesignFactory = $pdpGuestDesignFactory;
        $this->registry = $registry;
		$this->_pdpOptions = $pdpOptions;
        $this->messageManager = $messageManager;
    }
	
    /**
     * Customer login action
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
		$pdpGuestDesignId = $this->_pdpIntegrationSession->getPdpDesignId();
		if($pdpGuestDesignId) {
			$this->_pdpIntegrationSession->setPdpDesignId(null);
			$customerId = $this->_customerSession->getCustomerId();
			$this->_loadCustomerQuote();
			if($customerId) {
				try {
					$_dataGuestDesign = $this->_pdpGuestDesignFactory->create()->load($pdpGuestDesignId);
					if($_dataGuestDesign->getEntityId()) {
						$_dataItemVal = unserialize($_dataGuestDesign->getItemValue());
					}
					$dataGuestDesign = $this->_pdpGuestDesignFactory->create()->loadByCustomerId($customerId);
					if($dataGuestDesign->getEntityId() && !$_dataGuestDesign->getCustomerId() && $_dataGuestDesign->getCustomerIsGuest() && $_dataGuestDesign->getEntityId()) {
						try {
							$this->_pdpGuestDesignFactory->create()->setId($pdpGuestDesignId)->delete();
							//$this->_pdpIntegrationSession->setPdpDesignId(null);
						} catch(\Exception $e) {
							$this->messageManager->addException($e, __('Load guest design error'));
						}
						$dataItemVal = unserialize($dataGuestDesign->getItemValue());
						if(isset($_dataItemVal)) {
							foreach($_dataItemVal as $_item) {
								$dataItemVal[] = $_item;
							}
						}
						$dataGuestDesign->setItemValue(serialize($dataItemVal))->save();									
					} else {
						if($_dataGuestDesign->getEntityId()) {
							$this->_pdpGuestDesignFactory->create()
														 ->load($pdpGuestDesignId)
														 ->setCustomerIsGuest(0)
														 ->setCustomerId($customerId)
														 ->save();
						}
					}
				} catch(\Exception $e) {
					$this->messageManager->addException($e, __('Load guest design error'));
				}
			}
		}
	}
	
	/**
	 * @return void
	 */
	private function _loadCustomerQuote() {
		$pdpQuoteItems = $this->registry->registry('pdp_quote_item');
		if($pdpQuoteItems) {
			$this->registry->unregister('pdp_quote_item');
			$dataObjQuoteItem = unserialize($pdpQuoteItems->getPdpQuoteItem());
			try {
				$customerQuote = $this->getQuoteRepository()->getForCustomer($this->_customerSession->getCustomerId());
				$quoteItems = $customerQuote->getAllVisibleItems();
				foreach($quoteItems as $item) {
					$itemId = $item->getItemId();
					
					$modelPdpquote = $this->getPdpQuoteFactory()->create();
					$dataItem = $modelPdpquote->loadByItemId($itemId);
					$proId = 0;
					if(!$dataItem->getPdpcartId()) {
						$pdpProdu = $this->getPdpProductType()->getProductWithSku($item->getSku());
						foreach($pdpProdu as $key => $value) {
							if(isset($value['type_id'])) {
								$proId = $value['type_id'];
							}
						}
						$product = $item->getProduct();
						if($product->getTypeId() == 'pdpro') {
							if(!$proId) $proId = $item->getProductId();
							$requestOptions = $item->getBuyRequest();
							foreach($dataObjQuoteItem as $_key => $dataQuote) {
								if($requestOptions->getDesignId() == $dataQuote['design_id'] && $item->getProductId() == $dataQuote['product_id'] && $proId == $dataQuote['pdp_product_id']) {
									$updatePdpQuoteCart = true;
									if($requestOptions->getPdpOptions()) {
										if(isset($dataQuote['value']['pdp_options'])) {
											$infoRequest = $this->_pdpOptions->getOptInfoRquest($dataQuote['value']['pdp_options']);
											$diffOptions = $this->_pdpOptions->comparisonArrays($infoRequest['pdp_options'], $requestOptions->getPdpOptions());
											if(count($diffOptions)) {
												$updatePdpQuoteCart = false;
											}
										} else {
											$updatePdpQuoteCart = false;
										}
									}
									if($requestOptions->getPdpPrintType() && $updatePdpQuoteCart) {
										if(isset($dataQuote['value']['pdp_print_type'])) {
											$printType = $dataQuote['value']['pdp_print_type'];
											if(isset($printType['value']) && $printType['value'] != $requestOptions->getPdpPrintType()) {
												$updatePdpQuoteCart = false;
											}
										} else {
											$updatePdpQuoteCart = false;
										}
									}
									if($requestOptions->getPdpProductColor() && $updatePdpQuoteCart) {
										if(isset($dataQuote['value']['product_color'])) {
											$productColor = $dataQuote['value']['product_color'];
											if(isset($productColor['color_id']) && $productColor['color_id'] != $requestOptions->getPdpProductColor()) {
												$updatePdpQuoteCart = false;
											}
										} else {
											$updatePdpQuoteCart = false;
										}
									}
									if($updatePdpQuoteCart) {
										$data = array(
											'item_id' => $itemId,
											'product_id' => $item->getProductId(),
											'pdp_product_id' => $proId,
											'sku' => $item->getSku(),
											'url' => $dataQuote['url'],
											'store_id' => $item->getStoreId(),
											'design_id' => $dataQuote['design_id'],
											'value' => serialize($dataQuote['value'])
										);
										$this->getPdpQuoteFactory()->create()->addData($data)->save();
										unset($dataObjQuoteItem[$_key]);
									}
								}
							}
						}
					}
				}
			} catch(\Exception $e) {
				$this->messageManager->addException($e, __('Load guest design error'));
			}			
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
	
	/**
	 * @return PdpProductType
	 */
	private function getPdpProductType() {
        if (!$this->pdpProductType) {
            $this->pdpProductType = \Magento\Framework\App\ObjectManager::getInstance()->get(\PDP\Integration\Model\PdpProductType::class);
        }
        return $this->pdpProductType;
	}
}
<?php
namespace PDP\Integration\Model\Pdpproduct;

use PDP\Integration\Api\PdpGuestDesignRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use PDP\Integration\Api\Data\PdpDesignItemInterface;

class PdpGuestDesignRepository implements PdpGuestDesignRepositoryInterface {
	
    /**
     * @var DataObjectFactory
     */
    protected $objectFactory;
	
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
	
    /**
     * @var \PDP\Integration\Model\PdpGuestDesignFactory
     */
    protected $_pdpGuestDesignFactory;
	
    /**
     * @var \PDP\Integration\Api\Data\PdpReponseInterfaceFactory
     */
    protected $pdpReponseFactory;
	
    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;
	
    /**
     * PDP Integration session
     *
     * @var \PDP\Integration\Model\Session
     */
    protected $_pdpIntegrationSession;
	
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	
	/**
	 * @param DataObjectFactory $objectFactory
	 * @param ProductRepositoryInterface $productRepository
	 * @param \PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory
	 * @param \PDP\Integration\Api\Data\PdpReponseInterfaceFactory $pdpReponseFactory
	 * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
	 * @param \PDP\Integration\Model\Session $pdpIntegrationSession
	 * @param \Magento\Customer\Model\Session $customerSession
	 */
	public function __construct(
		DataObjectFactory $objectFactory,
		ProductRepositoryInterface $productRepository,
		\PDP\Integration\Model\PdpGuestDesignFactory $pdpGuestDesignFactory,
		\PDP\Integration\Api\Data\PdpReponseInterfaceFactory $pdpReponseFactory,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		\PDP\Integration\Model\Session $pdpIntegrationSession,
		\Magento\Customer\Model\Session $customerSession
	) {
		$this->objectFactory = $objectFactory;
		$this->productRepository = $productRepository;
		$this->_pdpGuestDesignFactory = $pdpGuestDesignFactory;
		$this->pdpReponseFactory = $pdpReponseFactory;
		$this->_pdpOptions = $pdpOptions;
		$this->_pdpIntegrationSession = $pdpIntegrationSession;
		$this->_customerSession = $customerSession;
	}
	
    /**
     * Perform persist operations for one entity
     *
     * @param PdpDesignItemInterface $entity
     * @return \PDP\Integration\Api\Data\PdpReponseInterface
     */
    public function save(\PDP\Integration\Api\Data\PdpDesignItemInterface $pdpDesignItem)
    {
		$reponse = $this->pdpReponseFactory->create();
		if($this->_pdpOptions->statusPdpIntegration()) {
			if($pdpDesignItem->getDesignId() && $pdpDesignItem->getProductId() && $pdpDesignItem->getProductSku()) {
				$product = $this->productRepository->get($pdpDesignItem->getProductSku());
				if($product->getTypeId()) {
					$modelGuestDesign = $this->_pdpGuestDesignFactory->create();
					$dataGuestDesign = array();
					$itemValue = array(
						'product_id' => $product->getEntityId(),
						'pdp_product_id' => $pdpDesignItem->getProductId(),
						'design_id' => $pdpDesignItem->getDesignId()
					);
					if($this->_customerSession->isLoggedIn()) {
						$customerId = $this->_customerSession->getCustomerId();
						$pdpGuestDesignId = $this->_pdpIntegrationSession->getPdpDesignId();
						if($pdpGuestDesignId) {
							if($customerId) {
								try {
									$_dataGuestDesign = $modelGuestDesign->load($pdpGuestDesignId);
									if($_dataGuestDesign->getEntityId()) {
										$_dataItemVal = unserialize($_dataGuestDesign->getItemValue());
									}
									try {
										if(!$_dataGuestDesign->getCustomerId() && $_dataGuestDesign->getCustomerIsGuest()) {
											$modelGuestDesign->setId($pdpGuestDesignId)->delete();
										}
										$this->_pdpIntegrationSession->setPdpDesignId(null);
									} catch(\Magento\Framework\Exception\LocalizedException $e) {
										$reponse->setStatus(false)
												->setMessage(nl2br($e->getMessage()));
										return $reponse;
									}
									$dataGuestDesign = $modelGuestDesign->loadByCustomerId($customerId);
									if($dataGuestDesign->getEntityId()) {
										$dataItemVal = unserialize($dataGuestDesign->getItemValue());
										if(isset($_dataItemVal)) {
											foreach($_dataItemVal as $_item) {
												$dataItemVal[] = $_item;
											}
										}
										$dataItemVal[] = $itemValue;
										$dataGuestDesign->setItemValue(serialize($dataItemVal))->save();									
									}
								} catch(\Magento\Framework\Exception\LocalizedException $e) {
									$reponse->setStatus(false)
											->setMessage(nl2br($e->getMessage()));
									return $reponse;
								}
							}
						} else {
							if($customerId) {
								try {
									$dataGuestDesign = $modelGuestDesign->loadByCustomerId($customerId);
									if($dataGuestDesign->getEntityId()) {
										$dataItemVal = unserialize($dataGuestDesign->getItemValue());
										$dataItemVal[] = $itemValue;
										$dataGuestDesign->setItemValue(serialize($dataItemVal))->save();									
									}
								} catch(\Magento\Framework\Exception\LocalizedException $e) {
									$reponse->setStatus(false)
											->setMessage(nl2br($e->getMessage()));
									return $reponse;
								}
							}
						}
					} else {
						if($this->_pdpIntegrationSession->getPdpDesignId()) {
							$pdpGuestDesignId = $this->_pdpIntegrationSession->getPdpDesignId();
							try {
								$dataGuestDesign = $modelGuestDesign->load($pdpGuestDesignId);
								$dataItemVal = unserialize($dataGuestDesign->getItemValue());
								$dataItemVal[] = $itemValue;
								$dataGuestDesign->setItemValue(serialize($dataItemVal))->save();
							} catch(\Magento\Framework\Exception\LocalizedException $e) {
								$reponse->setStatus(false)
										->setMessage(nl2br($e->getMessage()));
								return $reponse;
							}
						} else {
							try {
								$dataGuestDesign['item_value'] = serialize([$itemValue]);
								$dataGuestDesign['customer_is_guest'] = 1;
								$modelGuestDesign->addData($dataGuestDesign)->save();
								$pdpGuestDesignId = $modelGuestDesign->getEntityId();
								$this->_pdpIntegrationSession->setPdpDesignId($pdpGuestDesignId);
							} catch(\Magento\Framework\Exception\LocalizedException $e) {
								$reponse->setStatus(false)
										->setMessage(nl2br($e->getMessage()));
								return $reponse;
							}
						}
					}
					$reponse->setStatus(true)
							->setMessage('add data success');
				} else {
					$reponse->setStatus(false)
							->setMessage('post data failed, product not exists');
				}
			} else {
				$reponse->setStatus(false)
						->setMessage('post data failed');				
			}
		} else {
			$reponse->setStatus(false)
					->setMessage('post data failed, PDP Integration is not enable');
		}
		
		return $reponse;
	}	
}
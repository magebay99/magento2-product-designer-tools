<?php
namespace PDP\Integration\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use PDP\Integration\Model\PdpproductFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;	

    /**
     * @var Pdpquote
     */
    protected $pdpquote;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;	
	
    /**
     * @var ConfigurationPool
     */
    private $itemConverter;	
	
    /**
     * @var PdpproductFactory
     */
    protected $_pdpproductFactory;	

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
	 * @param \PDP\Integration\Model\ResourceModel\Pdpquote $pdpquote
	 * @param CartRepositoryInterface $quoteRepository
	 * @param ItemConverter $converter
	 * @param PdpproductFactory $pdpproductFactory
	 * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
		\PDP\Integration\Model\ResourceModel\Pdpquote $pdpquote,
		CartRepositoryInterface $quoteRepository,
		ItemConverter $converter,
		PdpproductFactory $pdpproductFactory,
		ProductRepositoryInterface $productRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->productRepository = $productRepository;
		$this->_pdpproductFactory = $pdpproductFactory;
		$this->quoteRepository = $quoteRepository;
		$this->itemConverter = $converter;
		$this->pdpquote = $pdpquote;
        parent::__construct($context);
    }
	
    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
	
    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
			/*$customerId = 2;
			$orders = $this->getOrderCollectionFactory()->create($customerId)->addFieldToSelect(
                array('entity_id','state','status')
            )->setOrder(
                'created_at',
                'desc'
            );
			foreach($orders as $_order) {
				echo $_order->getEntityId();
				echo '<br/>';
				echo $_order->getState();
				echo '<br/>';
				echo $_order->getStatus();die;
				echo '<pre>';print_r(get_class_methods($_order->getItemsCollection()));die;
			}*/
		$id = (int)$this->getRequest()->getParam('id');
		if($id) {
			$pdpproductModel = $this->_pdpproductFactory->create();
			$pdpproduct = $pdpproductModel->load($id);
			//$product = $this->_initProduct($pdpproduct->getProductId());
		}
		$resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Form Submit PDP Product'));
		$this->_objectManager->get('\Magento\Framework\Registry')->register('pdp_current_pdpproduct', $pdpproduct);
        return $resultPage;
    }
	
    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    private function getOrderCollectionFactory()
    {
        return $this->_objectManager->get(CollectionFactoryInterface::class);
    }
}
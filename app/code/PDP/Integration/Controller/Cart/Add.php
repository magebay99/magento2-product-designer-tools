<?php
namespace PDP\Integration\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use PDP\Integration\Model\PdpproductFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \PDP\Integration\Controller\Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;
	
    /**
     * @var Pdpquote
     */
    protected $pdpquote;	
	
    /**
     * @var PdpproductFactory
     */
    protected $_pdpproductFactory;	
	
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param PdpproductFactory $pdpproductFactory
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
		\PDP\Integration\Model\ResourceModel\Pdpquote $pdpquote,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		PdpproductFactory $pdpproductFactory,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
		$this->pdpquote = $pdpquote;
		$this->_pdpOptions = $pdpOptions;
        $this->pdpproductFactory = $pdpproductFactory;
        $this->productRepository = $productRepository;
    }
	
    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
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
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
		$pdpProductId = $params['pdp_product'];
		$pdpProduct = $this->pdpproductFactory->create()->load($pdpProductId);
		$_params = unserialize($pdpProduct->getValue());
		$pdp_option_data = $_params['pdp_options'];
		$pdp_print_type = $_params['pdp_print_type'];
		$pdpOptSelect = $this->_pdpOptions->getOptionsSelect($pdp_option_data);
		$infoRequest = $this->_pdpOptions->getOptInfoRquest($pdpOptSelect);
		$additionalOptions = $this->_pdpOptions->getAdditionOption($pdpOptSelect);
		if(count($pdp_print_type)) {
			$printType = array('label' => 'Print type', 'value' => '');
			$printType['value'] = $pdp_print_type['title'];
			if(isset($pdp_print_type['price'])) $printTypePrice = $pdp_print_type['price'];
			$printTypeValue = $pdp_print_type['value'];
			$additionalOptions[] = $printType;
			if(isset($printTypeValue)) {
				$infoRequest['pdp_print_type'] = $printTypeValue;
				if(isset($printTypePrice))
					$infoRequest['pdp_price'] += $printTypePrice;
			}
		}
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
			
            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }
			$infoRequest['product'] = $params['product'];
			$infoRequest['qty'] = $params['qty'];
			$product->addCustomOption('additional_options', serialize($additionalOptions));
			
            $this->cart->addProduct($product, $infoRequest);

            $this->cart->save();

            /**
             * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
             */
            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
					$itembypro = $this->cart->getQuote()->getItemByProduct($product);
					if($itembypro != false) {
						$itemId = $itembypro->getItemId();
					}
                    $message = __(
                        'You added %1 to your shopping cart.',
                        $product->getName()
						
                    );
					$model = $this->_objectManager->get('PDP\Integration\Model\Pdpquote');
					try {
						$data = array(
							'item_id' => $itemId,
							'product_id' => $itembypro->getProductId(),
							'store_id' => $itembypro->getStoreId(),
							'value' => serialize($_params)
						);
						$dataItem = $this->pdpquote->_loadByItemId($itemId);
						if(count($dataItem)) {
							$data['pdpcart_id'] = $dataItem[0];
						}
						$model->addData($data);						
						$model->save();
					} catch (\Magento\Framework\Exception\LocalizedException $e) {
						$this->messageManager->addError(nl2br($e->getMessage()));
					} catch (\Exception $e) {
						$this->messageManager->addException($e, __('Something went wrong while saving this '.$e->getMessage()));
					}
                    $this->messageManager->addSuccessMessage($message);
					
                }
                //return $this->goBack(null, $product);
				return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNotice(
                    $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addError(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                    );
                }
            }

            $url = $this->_checkoutSession->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            return $this->goBack($url);

        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            return $this->goBack();
        }
    }
	
    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];

        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        } else {
            if ($product && !$product->getIsSalable()) {
                $result['product'] = [
                    'statusText' => __('Out of stock')
                ];
            }
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }	
}
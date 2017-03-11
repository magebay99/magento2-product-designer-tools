<?php
namespace PDP\Integration\Block\Cart\Item;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer {
	
	/**
	 * @var boolean
	 */
	protected $_script = true;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
	 * @param \PDP\Integration\Helper\PdpOptions
	 */
	protected $_pdpOptions;		
	
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_pdpOptions = $pdpOptions;
        parent::__construct($context, $productConfig, $checkoutSession, $imageBuilder, $urlHelper, $messageManager, $priceCurrency, $moduleManager, $messageInterpretationStrategy, $data);
    }	
	
	/**
	 * @return boolean
	 */
	public function loadScript() {
		$pdpIntegrationSession = $this->_objectManager->create('PDP\Integration\Model\Session');
		if($this->_script && $pdpIntegrationSession->getPdpCheckoutCart()) {
			$pdpIntegrationSession->setPdpCheckoutCart(0);
			$this->_script = false;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getHtmlCustomDesign() {
		$item = $this->getItem();
		$html = '';
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpProductId = $pdpCart[0]['pdp_product_id'];
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->load($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html = '<ul class="items">';
				$i=0;
				foreach($sideThubms as $sideThub) {
					if($sideThub['thumb']) {
						$i++;
						$last = $i%2==0?'last':'';
						$html .= '<li style="display:inline-block;margin-right:5px;" class="item '.$last.'"><img style="border:1px solid #C1C1C1;" width="66" src="'.$urlTool.'/'.$sideThub['thumb'].'" /></li>';
					}
				}
				$html .= '</ul>';
			}
		}
		return $html;
	}
}
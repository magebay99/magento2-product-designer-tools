<?php
namespace PDP\Integration\Block\Cart\Item;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
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
     * @var Json
     */
    private $serializer;	
	
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
	 * @param Json $serializer
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
		Json $serializer = null,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
		$this->serializer = $serializer ?: $this->_objectManager->get(Json::class);
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
	public function getHtmlCustomDesignNameNumber() {
		$item = $this->getItem();
		$html = '';
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpProductId = $pdpCart[0]['pdp_product_id'];
			$html .= $this->getHtmlNameNumber($pdpCart[0]['value'], $item);
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->load($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html .= '<strong style="margin-bottom:5px;display:block;">'.__('Customized Design:').'</strong>';
				$html .= '<ul class="items">';
				$i=0;
				foreach($sideThubms as $sideThub) {
					if($sideThub['thumb']) {
						$i++;
						$last = $i%2==0?'last':'';
						$html .= '<li style="display:inline-block;margin-right:5px;" class="item '.$last.'"><a href="'.$urlTool.'/'.$sideThub['thumb'].'" target="_blank"><img style="border:1px solid #C1C1C1;" width="66" src="'.$urlTool.'/'.$sideThub['thumb'].'" /></a></li>';
					}
				}
				$html .= '</ul>';
			}
		}
		return $html;
	}
	
	/**
	 * @param String $value
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return String
	 */
	protected function getHtmlNameNumber($value, $item) {
		$_value = $this->serializer->unserialize($value);
		if(isset($_value['multi_size'])) {
			$html = '<div class="block-name-num">';
			$html .= '<strong>'.__('Name & Number').': </strong><i id="name-num-'.$item->getItemId().'" data-itemid="'.$item->getItemId().'" 
			data-mage-init=\'{"PDP_Integration/js/moreinfo":{"template_id":"#name-num'.$item->getItemId().'-template", "title": "'.__('Name & Number').'"}}\'
			class="more-info-name-num">more info</i>';
			$html .= '<script id="name-num'.$item->getItemId().'-template" type="x-magento-template">';
				$html .= '<div class="block-namenum">';
					$html .='<table class="data-grid data table">';
						$html .= '<thead>';
							$html .= '<tr>';
								$html .= '<th class="data-grid-th _col-xs">'.__('Name').'</th>';
								$html .= '<th class="data-grid-th _col-xs">'.__('Num').'</th>';
								$html .= '<th class="data-grid-th _col-xs">'.__('Size').'</th>';
								$html .= '<th class="data-grid-th _col-xs">'.__('Qty').'</th>';
							$html .= '</tr>';
							$html .= '<tr>';
						$html .= '</thead>';
						$html .= '<tbody>';
							foreach($_value['multi_size'] as $_item) {
								$html .= '<tr>';
									$html .= '<td class="data-grid-indicator-cell">'.$_item['name'].'</td>';
									$html .= '<td class="data-grid-indicator-cell">'.$_item['num'].'</td>';
									$html .= '<td class="data-grid-indicator-cell">'.$_item['size'].'</td>';
									$html .= '<td class="data-grid-indicator-cell">'.$_item['qty'].'</td>';
								$html .= '</tr>';
							}
						$html .= '</tbody>';
					$html .= '</table>';
				$html .= '</div>';
			$html .= '</script>';
			$html .="</div>";
		} else {
			$html = '';
		}
		return $html;
	}
}
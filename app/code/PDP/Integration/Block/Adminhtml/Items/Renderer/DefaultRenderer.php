<?php
namespace PDP\Integration\Block\Adminhtml\Items\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Renderer\DefaultRenderer {
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
	 * @param \PDP\Integration\Helper\PdpOptions
	 */
	protected $_pdpOptions;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_pdpOptions = $pdpOptions;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
    }
	
	/**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return string
	 */
	protected function __getHtmlCustomDesign(\Magento\Framework\DataObject $item) {
		$html = '';
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getQuoteItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->load($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html = '<span style="display:block;margin-bottom:5px;">'.__('Customized Design:').'</span>';
				$html .= '<ul class="items">';
				$i=0;
				foreach($sideThubms as $sideThub) {
					if($sideThub['thumb']) {
						$i++;
						$last = $i%2==0?'last':'';
						$html .= '<li class="item '.$last.'"><a href="'.$urlTool.'/'.$sideThub['thumb'].'" target="_blank"><img style="border:1px solid #C1C1C1;" width="143" src="'.$urlTool.'/'.$sideThub['thumb'].'" /></a></li>';
					}
				}
				$html .= '</ul>';
			}
		}
		return $html;
	}
	
	/**
	 * @return String
	 */
	public function getHtmlCustomDesign() {
		$_item = $this->getItem();
		if($_item instanceOf \Magento\Sales\Model\Order\Invoice\Item) {
			$item = $_item->getOrderItem();
		} elseif($_item instanceOf \Magento\Sales\Model\Order\Shipment\Item) {
			$item = $_item->getOrderItem();
		} elseif($_item instanceOf \Magento\Sales\Model\Order\Creditmemo\Item) {
			$item = $_item->getOrderItem();
		} else {
			$item = $_item;
		}
		return $this->__getHtmlCustomDesign($item);
	}
}
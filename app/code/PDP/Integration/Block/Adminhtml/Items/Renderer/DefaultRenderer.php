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
			$html .= $this->getHtmlNameNumber($pdpCart[0]['value'], $item);
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->load($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html .= '<span style="display:block;margin-bottom:5px;">'.__('Customized Design:').'</span>';
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
	 * @param String $value
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return String
	 */
	protected function getHtmlNameNumber($value, \Magento\Framework\DataObject $item) {
		$_value = unserialize($value);
		if(isset($_value['multi_size'])) {
			$html = '<div class="block-name-num">';
			$html .= '<span>'.__('Name & Number').': <i id="name-num-'.$item->getQuoteItemId().'" data-itemid="'.$item->getQuoteItemId().'" 
			data-mage-init=\'{"moreinfo":{"template_id":"#name-num'.$item->getQuoteItemId().'-template", "title": "'.__('Name & Number').'"}}\'
			class="more-info-name-num">more info</i></span>';
			$html .= '<script id="name-num'.$item->getQuoteItemId().'-template" type="x-magento-template">';
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
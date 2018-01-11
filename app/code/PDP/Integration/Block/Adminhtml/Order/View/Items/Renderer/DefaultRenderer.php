<?php
namespace PDP\Integration\Block\Adminhtml\Order\View\Items\Renderer;

use Magento\Sales\Model\Order\Item;
use Magento\Framework\Serialize\Serializer\Json;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
	 * @param Json $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		Json $serializer = null,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_pdpOptions = $pdpOptions;
		$this->serializer = $serializer ?: $this->_objectManager->get(Json::class);
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $messageHelper, $checkoutHelper, $data);
    }
	
	/**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return string
	 */
	protected function getHtmlCustomDesign(\Magento\Framework\DataObject $item) {
		$html = '';
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getQuoteItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpProductId = $pdpCart[0]['pdp_product_id'];
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
			$html .= $this->getBlockButtonHtml($pdpProductId, $designId);
		}
		return $html;
	}
	
	/**
	 * @param String $value
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return String
	 */
	protected function getHtmlNameNumber($value, \Magento\Framework\DataObject $item) {
		$_value = $this->serializer->unserialize($value);
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
	 * @param int $productId
	 * @param int $designId
	 * @return string
	 */
	public function getLinkEditDesign($productId, $designId) {
		$url = $this->_pdpOptions->getUrlToolDesign();
		$param = '';
		if(!$productId || !$designId) {
			return $url;
		}
		if($designId) {
			$param .= '?export-design='.$designId;
		}
		if($productId) {
			$param .= '&pid='.$productId;
		}

		if(substr($url, -1) == '/') {
			$url .= $param;
		} else {
			$url .= '/'.$param;
		}
		return $url;
	}
	
	/**
	 * @param int $designId
	 * @return string
	 */
	public function getLinkZipDesign($designId) {
		$url = $this->_pdpOptions->getUrlToolDesign();
		if($designId) {
			if(substr($url, -1) == '/') {
				$url .= 'rest/design-download?id='.$designId;
			} else {
				$url .= '/rest/design-download?id='.$designId;
			}
		}
		return $url;
	}

    /**
     * @param int $pid
     * @param int $tid
     * @return string
     */
	public function getLinkUpdateSvgDesign($pid,$tid)
    {
        $urlEdit = $this->getLinkEditDesign($pid, $tid);
        $urlUpdateSvg = str_replace('tid', 'export-design', $urlEdit);
        $urlUpdateSvg = $urlUpdateSvg . '&force-update-svg=1';

        return $urlUpdateSvg;
    }

	/**
	 * @param int $productId
	 * @param int $designId
	 * @return string
	 */
	public function getBlockButtonHtml($productId, $designId) {
		$html = '';
		if(!$productId || !$designId) {
			return $html;
		}
		$html .= '<div class="block-button">';
			$html .= '<a class="zip-design" href="javascript:void(0)" data-mage-init=\'{"pdpzipdesign":{"url":"'.$this->getLinkZipDesign($designId).'","update-svg-url":"'.$this->getLinkUpdateSvgDesign($productId,$designId).'"}}\' >'.__('Download').'</a>';
			$html .= '<a class="edit-button" target="_blank" href="'.$this->getLinkEditDesign($productId, $designId).'">'.__('Open Editor').'</a>';
		$html .= '</div>';
		return $html;
	}
	
    /**
     * @param \Magento\Framework\DataObject|Item $item
     * @param string $column
     * @param null $field
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name');
				$html .= $this->getHtmlCustomDesign($item);
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
            case 'status':
                $html = $item->getStatus();
                break;
            case 'price-original':
                $html = $this->displayPriceAttribute('original_price');
                break;
            case 'tax-amount':
                $html = $this->displayPriceAttribute('tax_amount');
                break;
            case 'tax-percent':
                $html = $this->displayTaxPercent($item);
                break;
            case 'discont':
                $html = $this->displayPriceAttribute('discount_amount');
                break;
            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }	
}
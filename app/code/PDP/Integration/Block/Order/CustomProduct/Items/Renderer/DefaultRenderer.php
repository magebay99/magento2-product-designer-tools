<?php
namespace PDP\Integration\Block\Order\CustomProduct\Items\Renderer;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;

class DefaultRenderer extends \PDP\Integration\Block\AbstractPdpAcc{

    /**
     * image size width
     */
    const SIZE_IMAGE_WIDTH = 207;    
	
	/**
	 * @param Magento\Catalog\Api\ProductRepositoryInterfaceFactory
	 */
	protected $_productRepositoryFactory;

    /**
	 * @param \PDP\Integration\Helper\PdpOptions
	 */
	protected $_pdpOptions;	
	
    /**
	 * @param \Magento\Quote\Model\Quote\Item
	 */
	protected $_quoteItem;		
	
	/**
     * @param \PDP\Integration\Block\Context $context
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param array $data
     */
    public function __construct(
        \PDP\Integration\Block\Context $context,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		ProductRepositoryInterfaceFactory $productRepositoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->_pdpOptions = $pdpOptions;
		$this->_productRepositoryFactory = $productRepositoryFactory;
    }
	
	/**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return \Magento\Quote\Model\Quote\Item
	 */
	protected function getQuoteItem(\Magento\Framework\DataObject $item) {
		$quoteItemId = $item->getQuoteItemId();
		$quoteItem = $this->_quoteItem = $this->_objectManager->get('Magento\Quote\Model\Quote\Item')->load($quoteItemId);
		return $quoteItem;
	}
	
	/**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return string
	 */
	protected function getPriceHtmlItem(\Magento\Framework\DataObject $item) {
		$html = '';
		$quoteItem = $this->getQuoteItem($item);
		if($quoteItem->getCustomPrice()) {
			$itemPrice = $quoteItem->getCustomPrice()*$quoteItem->getQty();
		} else {
			 $itemPrice = $this->_pdpOptions->getConvertedPrice($quoteItem->getPrice()*$quoteItem->getQty(), $item->getStoreId());
		}
		if($itemPrice) {
			$html = '<span class="box-price">';
				$html .= '<span class="item-price">';
				$html .= $this->_pdpOptions->formatPrice($itemPrice, $item->getStoreId());
				$html .= '</span>';
			$html .= '</span>';
		}
		return $html;
	}
	
    /**
	 * @param \Magento\Framework\DataObject|Item $item
     * @return array|null
     */
    protected function getOrderItem(\Magento\Framework\DataObject $item)
    {
        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            return item;
        } else {
            return $item->getOrderItem();
        }
    }
	
    /**
	 * @param \Magento\Framework\DataObject|Item $item
     * @return array
     */
    protected function getItemOptions(\Magento\Framework\DataObject $item)
    {
        $result = [];
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
	
    /**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return string
	 */
	protected function getHtmlProductDetail(\Magento\Framework\DataObject $item) {
		$html = '';
		$productId = $item->getProductId();
		$options  = $this->getItemOptions($item);
		$optHtml = '';
		if($options) {
			$optHtml = '<dl>';
			foreach($options as $option) {
				$optHtml .= '<dt><strong><em>'.$option['label'].'</em></strong></dt>';
				$optHtml .= '<dd>'.$option['value'].'</dd>';
			}
			$optHtml .= '</dl>';
		}
		$product = $this->_productRepositoryFactory->create()->getById($productId);
		if($product->getEntityId()) {
			$helperImage = $this->_objectManager->get('Magento\Catalog\Helper\Image');
			$imageUrl = $helperImage->init($product, 'product_page_image_medium')
				->constrainOnly(true)->keepAspectRatio(true)->keepFrame(false)
				->setImageFile($product->getFile())
				->resize(self::SIZE_IMAGE_WIDTH)
				->getUrl();
			$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getQuoteItemId());
			$html =  '<div class="product-detail" >';
				$html .= '<div class="product-image"><img width="'.self::SIZE_IMAGE_WIDTH.'" src="'.$imageUrl.'"/></div>';
				$html .= '<div class="block-info" style="float:left">
							  <ul class="items">
								<li>'.$item->getName().'</li>
								<li>SKU: '.$item->getSku().'</li>';
								if($optHtml) {
									$html .= '<li>'.$optHtml.'</li>';
								}
								if(count($pdpCart)) {
									$html .= $this->getHtmlNameNumber($pdpCart[0]['value'], $item);
								}
							  $html .= '</ul>
						  </div>';
			$html .= '</div>';
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
			data-mage-init=\'{"PDP_Integration/js/moreinfo":{"template_id":"#name-num'.$item->getQuoteItemId().'-template", "title": "'.__('Name & Number').'"}}\'
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
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return string
	 */
	protected function getHtmlCustomDesign(\Magento\Framework\DataObject $item) {
		$html = '';
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getQuoteItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->getDesignByDesignId($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html = '<ul class="items">';
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
				$html = $this->getHtmlProductDetail($item);
                break;
            case 'status':
				//$sku = $item->getSku();
				$urlTool = $this->_pdpOptions->getUrlToolDesign();
				$designId = 0;
				$urlEdit = '#';
				$urlUpdateSvg = '#';
				$itemId = $item->getQuoteItemId();
				$pdpQuoteData = $this->_pdpOptions->getPdpCartItem($itemId);
				if(count($pdpQuoteData)) {
					$urlEdit = $pdpQuoteData[0]['url'];
					$designId = $pdpQuoteData[0]['design_id'];
					$urlUpdateSvg = str_replace('tid', 'export-design', $urlEdit);
					$urlUpdateSvg = $urlUpdateSvg . '&force-update-svg=1';
				}
				$param = '';
				if($designId) {
					$param .= 'rest/design-download?id='.$designId.'&zip=1';
				} else {
					$param = '#';
				}
				if(substr($urlTool, -1) == '/') {
					$urlTool .= $param;
				} else {
					$urlTool .= '/'.$param;
				}
				$html .= '<span class="item-status">';
				$html .= __($this->getStatus());
				$html .= '</span>';
				$html .= '<div class="block-button">';
					if($this->getStatus() == 'complete') {
						$html .= '<a class="zip-design" href="javascript:void(0)" data-href="'.$urlTool.'" data-editor-uri="' . $urlUpdateSvg . '">'.__('Zip Design').'</a>';
					}
					$html .= '<a class="edit-button" href="'.$urlEdit.'">'.__('Edit Design').'</a>';
				$html .= '</div>';
                break;
            case 'price':
				$html = $this->getPriceHtmlItem($item);
                break;
            case 'customdesign':
				$html = $this->getHtmlCustomDesign($item);
                break;
        }
        return $html;
    }
}
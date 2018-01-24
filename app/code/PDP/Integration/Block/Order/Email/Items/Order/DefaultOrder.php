<?php
namespace PDP\Integration\Block\Order\Email\Items\Order;

use Magento\Sales\Model\Order\Item as OrderItem;

class DefaultOrder extends \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder{
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
	 * @param \PDP\Integration\Helper\PdpOptions
	 */
	protected $_pdpOptions;
	
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_objectManager = $objectManager;
        $this->_pdpOptions = $pdpOptions;
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
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->getDesignByDesignId($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				$html = '<strong style="display:block;margin-bottom:5px;">'.__('Customized Design:').'</strong>';
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
		$item = $this->getItem();
		return $this->__getHtmlCustomDesign($item);
	}
}
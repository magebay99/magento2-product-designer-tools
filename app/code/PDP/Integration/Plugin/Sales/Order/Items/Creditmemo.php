<?php
namespace PDP\Integration\Plugin\Sales\Order\Items;

class Creditmemo {
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
	 * @param \PDP\Integration\Helper\PdpOptions
	 */
	protected $_pdpOptions;

    /**
	 * @param \PDP\Integration\Helper\File\Media
	 */
	protected $_helperMedia;
	
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \PDP\Integration\Helper\PdpOptions $pdpOptions
     */
    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\PDP\Integration\Helper\PdpOptions $pdpOptions,
		\PDP\Integration\Helper\File\Media $helperMedia
    ) {
        $this->_objectManager = $objectManager;
        $this->_pdpOptions = $pdpOptions;
        $this->_helperMedia = $helperMedia;
    }
	
    /**
     * Custom data to result
     *
     * @param \PDP\Integration\Model\Sales\Order\Pdf\Items\Creditmemo $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetImagesCustomDesign(\PDP\Integration\Model\Sales\Order\Pdf\Items\Creditmemo $subject, $result)
    {
		$_item = $subject->getItem();
		if($_item instanceOf \Magento\Sales\Model\Order\Invoice\Item) {
			$item = $_item->getOrderItem();
		} elseif($_item instanceOf \Magento\Sales\Model\Order\Shipment\Item) {
			$item = $_item->getOrderItem();
		} elseif($_item instanceOf \Magento\Sales\Model\Order\Creditmemo\Item) {
			$item = $_item->getOrderItem();
		} else {
			$item = $_item;
		}
		$result = $this->__getImagesCustomDesign($item);
        return $result;
    }
	
	/**
	 * @param \Magento\Framework\DataObject|Item $item
	 * @return array
	 */
	protected function __getImagesCustomDesign(\Magento\Framework\DataObject $item) {
		$arrayUrlImage = array();
		$pdpCart = $this->_pdpOptions->getPdpCartItem($item->getQuoteItemId());
		if(count($pdpCart)) {
			$urlTool = $this->_pdpOptions->getUrlToolDesign();
			$designId = $pdpCart[0]['design_id'];
			$pdpDesignJson = $this->_objectManager->get('PDP\Integration\Model\PdpDesignJson')->getDesignByDesignId($designId);
			if($pdpDesignJson->getDesignId()) {
				$sideThubms = unserialize($pdpDesignJson->getSideThumb());
				foreach($sideThubms as $sideThub) {
					if($sideThub['thumb']) {
						$urlImg = $urlTool.'/'.$sideThub['thumb'];
						$_urlImg = $this->_helperMedia->uploadImage($urlImg);
						if($_urlImg != null) {
							$arrayUrlImage[] = $_urlImg;
						}
					}
				}
			}
		}
		return $arrayUrlImage;
	}	
}
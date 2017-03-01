<?php
namespace PDP\Integration\Block\Cart\Item\Renderer\Actions;

class Edit extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Edit
{
    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'checkout/cart/configure',
            [
                'id' => $this->getItem()->getId(),
                'product_id' => $this->getItem()->getProduct()->getId()
            ]
        );
    }
	
	/**
	 * @param string $sku
	 * @return String
	 */
	public function getLinkDesignPdp() {
		$item = $this->getItem();
		$itemId = $item->getItemId();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		return $objectManager->get('PDP\Integration\Helper\PdpOptions')->getLinkDesignPdpWithItemId($itemId);
	}	
}
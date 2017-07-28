<?php
namespace PDP\Integration\Plugin\Checkout\Block\Cart;

class AbstractCart {
	
    /**
     * @param \Magento\Checkout\Block\Cart\AbstractCart $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetItemHtml(
		\Magento\Checkout\Block\Cart\AbstractCart $subject,
		 \Closure $proceed,
		\Magento\Quote\Model\Quote\Item $item
	) {
		$item->setProductType(\PDP\Integration\Model\Product\Type\Pdpro::TYPE_CODE);
		$result = $proceed($item);
		//$renderer = $subject->getItemRenderer('pdpro')->setItem($item);
		//echo $subject->getItem()->getProduct()->getName(); die;
		return $result;
	}
}
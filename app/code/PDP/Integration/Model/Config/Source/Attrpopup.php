<?php
namespace PDP\Integration\Model\Config\Source;

class Attrpopup implements \Magento\Framework\Option\ArrayInterface {
	
	/**
	 * Gets options popup
	 *
	 * @return Array
	 */
    public function toOptionArray()
    {
        return [
            '0' => __('No'),
            '1'   => __('Lightbox'),
            '2'   => __('Inline page')
        ];
    }	
}
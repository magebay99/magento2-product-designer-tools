<?php
namespace PDP\Integration\Plugin;

class ProductConfiguration{
	
	/**
	 * @param \Magento\Catalog\Helper\Product\Configuration $subject
	 * @param array $result
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function afterGetCustomOptions(\Magento\Catalog\Helper\Product\Configuration $subject, $result) {
		if(is_array($result)) {
			$newOptions = array();
			foreach($result as $key => $item) {
				if($item['label'] == 'Design Id' && strpos($item['value'], 'pdp_design_id') == 0) {
					continue;
				}
				$newOptions[] = $item;
			}
			if(count($newOptions)) {
				$result = $newOptions;
			}
		}
		return $result;
	}	
}
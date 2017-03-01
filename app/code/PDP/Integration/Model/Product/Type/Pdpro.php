<?php
namespace PDP\Integration\Model\Product\Type;

class Pdpro extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    /**
     * Product type
     */
    const TYPE_CODE = 'pdpro';
	protected function _construct()
	{
		parent::_construct();
	}	
    /**
     * Delete data specific for Simple product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
		
    }
}
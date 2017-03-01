<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

class PdpProductType extends \Magento\Framework\Model\AbstractModel {
    
	/**
     * Define resource model
	 * @param Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        Context $context
    )
    {
        parent::__construct($context, $coreRegistry);
    }
	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PDP\Integration\Model\ResourceModel\PdpProductType');
    }
	
	/**
	 * @param string $sku
	 * @return array
	 */
	public function getProductWithSku($sku){
		$collection = $this->getCollection();
		$collection->addFieldToSelect(array('*'))
				   ->addFieldToFilter('sku',array('eq' => $sku))
		           ->setCurPage(1);
		return $collection;
	}
}
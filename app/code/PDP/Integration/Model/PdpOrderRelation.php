<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

/**
* @method \PDP\Integration\Model\ResourceModel\PdpOrderRelation _getResource()
*/
class PdpOrderRelation extends \Magento\Framework\Model\AbstractModel
{
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
        $this->_init('PDP\Integration\Model\ResourceModel\PdpOrderRelation');
    }
	
	/**
	 * @param string $id
	 * @return array
	 */
	public function getDataWithPdporderId($id){
		$collection = $this->getCollection();
		$collection->addFieldToSelect(array('*'))
				   ->addFieldToFilter('pdp_order_id',array('eq' => $id))
		           ->setCurPage(1);
		return $collection;
	}
	
	/**
	 * @param string $id
	 * @return array
	 */
	public function getDataWithOrderId($id){
		$collection = $this->getCollection();
		$collection->addFieldToSelect(array('*'))
				   ->addFieldToFilter('order_id',array('eq' => $id))
		           ->setCurPage(1);
		return $collection;
	}
}
<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

/**
* @method \PDP\Integration\Model\ResourceModel\Pdpquote _getResource()
*/
class Pdpquote extends \Magento\Framework\Model\AbstractModel
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
        $this->_init('PDP\Integration\Model\ResourceModel\Pdpquote');
    }
	
	/**
	 * get values
	 * param int $pdpcartId
	 * param array $fieldSelect
	 * @return array $data
	 **/
	function getPdpOptions($pdpcartId,$fieldSelect = array('*'))
	{
		$collection = $this->getCollection()
			->addFieldToSelect($fieldSelect)
			->addFieldToFilter('pdpcart_id',$pdpcartId);
		return $collection;
	}
	
    /**
     *
     * @param int $itemId
     * @return array
     */
    public function loadByItemId($itemId)
    {
        $this->_getResource()->loadByItemId($this, $itemId);
		return $this;
    }
	
    /**
     *
     * @param int $itemId
     * @return array
     */
    public function _loadByItemId($itemId)
    {
        return $this->_getResource()->_loadByItemId($itemId);
    }	
}
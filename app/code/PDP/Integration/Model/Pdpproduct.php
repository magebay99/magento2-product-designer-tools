<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;

class Pdpproduct extends AbstractModel
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
        $this->_init('PDP\Integration\Model\ResourceModel\Pdpproduct');
    }
	
	public function getPdpproWithProductId($productId,$arrayseletct = array('*'),$conditions = array(),$orderBy = 'updated_at',$sortOrder = 'DESC',$limit = 0,$curPage = 1)
	{
		$collection = $this->getCollection();
		$collection->addFieldToSelect($arrayseletct);
		if(count($conditions))
		{
			foreach($conditions as $key => $condition)
			{
				$collection->addFieldToFilter($key,$condition);
			}
		}
		$collection->addFieldToFilter('product_id',$productId);
		if($limit > 0)
		{
			$collection->setPageSize($limit);
		}
		$collection->setCurPage($curPage);
		$collection->setOrder($orderBy,$sortOrder);
		return $collection;
	}	
}
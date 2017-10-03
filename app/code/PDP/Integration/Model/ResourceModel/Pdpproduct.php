<?php
namespace PDP\Integration\Model\ResourceModel;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Pdpproduct extends AbstractDb
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    
	/**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        $this->_date = $date;
        parent::__construct($context, $resourcePrefix);
    }
	
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('pdp_product', 'entity_id');
    }
	
	public function getTable($tableName) {
		return 'pdp_product';
	}	
	
    /**
     * Process data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }

        if (!$object->getPublishTime()) {
            $object->setPublishTime($gmtDate);
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }	
}
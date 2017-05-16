<?php

namespace PDP\Integration\Model\ResourceModel;

class Pdpquote extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pdp_cart', 'pdpcart_id');
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
    /**
     * Get ids to which specified item is assigned
     * @param  int $itemId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($itemId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'item_id = ?',
            (int)$itemId
        );

        return $adapter->fetchCol($select);
    }

    public function loadByItemId($quote, $itemId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect(
            'item_id',
            $itemId,
            $quote
        )->order(
            'updated_at ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);

        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }
	
    public function _loadByItemId($itemId)
    {
		return $this->_lookupIds($itemId, 'pdp_cart', 'pdpcart_id');
	}	
}
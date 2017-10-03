<?php
namespace PDP\Integration\Model\ResourceModel;

class PdpProductType extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	
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
        $this->_init('pdp_product_type', 'type_id');
    }
	
	public function getTable($tableName) {
		return 'pdp_product_type';
	}	
	
    public function loadBySku($quote, $itemId)
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
}
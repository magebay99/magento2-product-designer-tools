<?php
namespace PDP\Integration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PdpDesignJson extends AbstractDb{
	
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
        $this->_init('pdp_design_json', 'design_id');
    }
	
	public function getTable($tableName) {
		return 'pdp_design_json';
	}	
}
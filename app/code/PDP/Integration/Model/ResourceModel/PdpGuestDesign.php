<?php
namespace PDP\Integration\Model\ResourceModel;

class PdpGuestDesign extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('pdp_guest_design', 'entity_id');
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

        if ($object->isObjectNew() && !$object->getCreatedAt()) {
            $object->setCreatedAt($gmtDate);
        }

        $object->setUpdatedAt($gmtDate);

        return parent::_beforeSave($object);
    }
	
    /**
     * Load pdp guest design data by customer identifier
     *
     * @param \PDP\Integration\Model\PdpGuestDesign $guestDesign
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($guestDesign, $customerId)
    {
        $connection = $this->getConnection();
        $select = $this->_getLoadSelect(
            'customer_id',
            $customerId,
            $guestDesign
        )->where(
            'is_active = ?',
            1
        )->order(
            'updated_at ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);

        if ($data) {
            $guestDesign->setData($data);
        }

        $this->_afterLoad($guestDesign);

        return $this;
    }
}
<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

class PdpGuestDesign extends \Magento\Framework\Model\AbstractModel {
	
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
        $this->_init('PDP\Integration\Model\ResourceModel\PdpGuestDesign');
    }
	
    /**
     * Loading guest design data by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        $this->_getResource()->loadByCustomerId($this, $customerId);
        return $this;
    }
}
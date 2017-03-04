<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

/**
* @method \PDP\Integration\Model\ResourceModel\PdpOrderItem _getResource()
*/
class PdpOrderItem extends \Magento\Framework\Model\AbstractModel
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
        $this->_init('PDP\Integration\Model\ResourceModel\PdpOrderItem');
    }
}
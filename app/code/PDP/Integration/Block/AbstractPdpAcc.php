<?php
namespace PDP\Integration\Block;

class AbstractPdpAcc extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;    
	
	/**
     * constructor
     *
     * @param \PDP\Integration\Block\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \PDP\Integration\Block\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
		$this->_objectManager = $context->getObjectManager();
		$this->_customerSession = $context->getCustomerSession();
    }
		
}
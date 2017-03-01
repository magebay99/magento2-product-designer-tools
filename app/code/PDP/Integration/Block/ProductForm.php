<?php

namespace PDP\Integration\Block;

class ProductForm extends \Magento\Framework\View\Element\Template{
    
    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @codeCoverageIgnore
     */	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->_coreRegistry = $coreRegistry;
    }
	
	public function getPdpProduct() {
        if (!$this->hasData('pdp_product')) {
            $this->setData('pdp_product',
                $this->_coreRegistry->registry('pdp_current_pdpproduct')
            );
        }
        return $this->getData('pdp_product');		
	}
}
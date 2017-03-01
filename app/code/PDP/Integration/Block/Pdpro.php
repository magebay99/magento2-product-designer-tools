<?php
namespace PDP\Integration\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Directory\Model\Currency;

class Pdpro extends Template {
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
    */
    protected $_coreRegistry;

	/**
     *
     * @var Magento\Framework\Pricing\Helper\Data 
    */
	protected $_priceHelper;

	/**
     *
     * @var Magento\Directory\Model\Currency
    */
	protected $_currency;

	public function __construct(
		Template\Context $context,
		Registry $coreRegistry,
		PriceHelper $priceHelper,
		Currency $currency,
		array $data = []
	) 
	{
		$this->_coreRegistry = $coreRegistry;
		$this->_priceHelper = $priceHelper;
		$this->_timezone = $timezone;
		$this->_currency = $currency;
        parent::__construct($context, $data);
	}	
}
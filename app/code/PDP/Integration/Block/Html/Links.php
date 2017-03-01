<?php
namespace PDP\Integration\Block\Html;

class Links extends \Magento\Framework\View\Element\Template {
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		array $data = []
	){
		parent::__construct($context, $data);
	}
}
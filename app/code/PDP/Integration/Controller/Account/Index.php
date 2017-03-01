<?php
namespace PDP\Integration\Controller\Account;
use Magento\Framework\Controller\ResultFactory;

class Index extends \PDP\Integration\Controller\Account{
	
	public function execute(){
		/** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPageFactory = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		$resultPageFactory->getConfig()->getTitle()->set(__('My Customized Products'));
		return $resultPageFactory;
    }
}
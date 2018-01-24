<?php

namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;

class PdpProductType extends \Magento\Framework\Model\AbstractModel {

    /**
     * @param \PDP\Integration\Helper\PdpOptions
     */
    protected $_pdpOptions;

    /**
     * Define resource model
     * @param Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
    \Magento\Framework\Registry $coreRegistry, 
    \PDP\Integration\Helper\PdpOptions $pdpOptions,
    Context $context
    ) {
        $this->_pdpOptions = $pdpOptions;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @var \PDP\Integration\Helper\Curl
     * @author Zuko
     * @since v1.2
     */
    private $_curl;

    /**
     * @return \PDP\Integration\Helper\Curl
     * @author Zuko
     * @since v1.2
     */
    public function getCurl() {
        if (!$this->_curl) {
            $this->_curl = new \PDP\Integration\Helper\Curl();
        }
        return $this->_curl;
    }
    
    /**
     * @param $result
     * @return bool
     * @author Zuko
     * @since v1.2
     */
    private function checkCurlJson($result){
        if($result){
            if(is_array($result) && $result['status'] == 'success'){
                return true;
            }
        }
        return false;
    }
    

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct() {
        $this->_init('PDP\Integration\Model\ResourceModel\PdpProductType');
    }

    /**
     * @param string $sku
     * @return array
     */
    public function getProductWithSku($sku) {
        if($this->_pdpOptions->separateDatabase()){
            $url = rtrim($this->_pdpOptions->getUrlToolDesign(),'/');
            $url .= '/rest/commerce?product=1&sku=' . $sku;
            /* init curl */
            $this->getCurl()->setUrl($url);
            $result = $this->getCurl()->exec();
            if($result && $this->checkCurlJson($result))
            {
                return $result['data'];
            }
        }else{
            $collection = $this->getCollection();
            $collection->addFieldToSelect(array('*'))
                    ->addFieldToFilter('sku', array('eq' => $sku))
                    ->setCurPage(1);
            return $collection;
        }
    }

}
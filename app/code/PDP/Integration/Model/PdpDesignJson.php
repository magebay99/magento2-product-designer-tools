<?php
namespace PDP\Integration\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;

class PdpDesignJson extends AbstractModel{
	
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
    )
    {
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
    protected function _construct()
    {
        $this->_init('PDP\Integration\Model\ResourceModel\PdpDesignJson');
    }	
    
    /**
     * @param string $designId
     * @return array
     */
    public function getDesignByDesignId($designId) {
        if($this->_pdpOptions->separateDatabase()){
            $url = rtrim($this->_pdpOptions->getUrlToolDesign(),'/');
            $url .= '/rest/commerce?design&params_id=' . $designId;
            /* init curl */
            $this->getCurl()->setUrl($url);
            $result = $this->getCurl()->exec();
            if($result && $this->checkCurlJson($result))
            {
                $designJsonForm=new PdpDesignJsonForm();
                if(count($result['data'])>0){
                    $designJsonForm->setDesignId($result['data'][0]['design_id']);
                    $designJsonForm->setSideThumb($result['data'][0]['side_thumb_raw']);
                }
                return $designJsonForm;
            }
        }else{
            $result = $this->load($designId);
            return $result;
        }
    }
}
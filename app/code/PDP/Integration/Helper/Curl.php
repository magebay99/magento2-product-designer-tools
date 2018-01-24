<?php
/*
         M""""""""`M            dP                     
         Mmmmmm   .M            88                     
         MMMMP  .MMM  dP    dP  88  .dP   .d8888b.     
         MMP  .MMMMM  88    88  88888"    88'  `88     
         M' .MMMMMMM  88.  .88  88  `8b.  88.  .88     
         M         M  `88888P'  dP   `YP  `88888P'     
         MMMMMMMMMMM    -*-  Created by Zuko  -*-      

         * * * * * * * * * * * * * * * * * * * * *     
         * -    - -   F.R.E.E.M.I.N.D   - -    - *     
         * -  Copyright © 2017 (Z) Programing  - *     
         *    -  -  All Rights Reserved  -  -    *     
         * * * * * * * * * * * * * * * * * * * * *     
*//**
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
 * @PROJECT    : PDP - Magento 2.x Connector
 * @AUTHOR     : Zuko
 * @COPYRIGHT  : © 2017 Zuko
 * @LINK       : http://www.zuko.pw/
 * @FILE       : Curl.php
 * @CREATED    : 8:56 AM , 30/Nov/2017
 * @SINCE      : v1.2
* --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
**/
namespace PDP\Integration\Helper;
/**
 * Class PDP_Integration_Helper_Curl
 * OOP Curl wrapper
 */
class Curl {

    private $_curlHandle;

    protected $_currentUrl;

    /**
     * Default option for curl handle
     *
     * @var array
     */
    private $_curlDefaultOpt = array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_USERAGENT => 'X-PHP/cUrl transport',
        CURLOPT_HEADER => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 45
    );

    private $_options = array();
    private $_rawResponse;
    private $_resHeaders = array();
    private $_resContentType;
    public $response;

    /**
     * throw excecption when curl not loaded
     * @return bool
     * @throws \ErrorException
     */
    private function checkCurlLib()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded. You need cURL PHP Extension to use this method !');
        }
        return true;
    }

    /**
     * Initial default options
     */
    private function initCurlOpt()
    {
        if (is_array($this->_curlDefaultOpt) && count($this->_curlDefaultOpt))
        {
            foreach ($this->_curlDefaultOpt as $key => $value)
            {
                $this->setOpt($key, $value);
            }
        }
    }

    /**
     * @throws \ErrorException
     */
    private function init()
    {
        try
        {
            $this->checkCurlLib();
            $this->_curlHandle = curl_init();
            /* set curl opt */
            $this->initCurlOpt();
        }
        catch (ErrorException $e)
        {
            throw $e;
        }
    }

    /**
     * Fetch header info from curl response
     */
    private function getHeaderInfo()
    {
        $this->_resContentType = curl_getinfo($this->_curlHandle, CURLINFO_CONTENT_TYPE);
        $header_size = curl_getinfo($this->_curlHandle, CURLINFO_HEADER_SIZE);
        $headerContent = substr($this->_rawResponse, 0, $header_size);
        $this->_resHeaders = array();

        // Split the string on every "double" new line.
        $arrRequests = explode("\r\n\r\n", $headerContent);

        // Loop of response headers. The "count() -1" is to
        //avoid an empty row for the extra line break before the body of the response.
        for ($index = 0; $index < count($arrRequests) -1; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }
    }

    /**
     * Parse response body ,decode json(if match content type) , return parsed data
     *
     * @return bool|string
     */
    private function parseReponse()
    {
        $this->getHeaderInfo();
        $header_size = curl_getinfo($this->_curlHandle, CURLINFO_HEADER_SIZE);
        $response = substr( $this->_rawResponse, $header_size );
        if($this->_resContentType == 'application/json')
        {
            $this->response['json'] = json_decode($response,true);
            $this->response['simple'] = $response;
            return $this->response['json'];
        }else{
            $this->response = $response;
        }
        return $this->response;
    }

    /**
     * PDP_Integration_Helper_Curl constructor.
     *
     * @param null|sring $url
     * @throws \ErrorException
     */
    public function __construct($url = null)
    {
        try
        {
            $this->init();
            if($url) $this->setUrl($url);
        }
        catch (ErrorException $e)
        {
            throw $e;
        }
    }

    /**
     * Store Option value to _options array then set curl handle's option
     *
     * @param mixed $key
     * @param mixed $val
     */
    public function setOpt($key,$val)
    {
        $this->_options[$key] = $val;
        curl_setopt($this->_curlHandle,$key,$val);
    }

    /**
     * Execute curl session , return parsed data
     * @throws \ErrorException
     */
    public function exec()
    {
        if(!$this->_currentUrl) throw new \ErrorException('no Url was set');
        $this->_rawResponse = curl_exec ($this->_curlHandle);
        return $this->parseReponse();
    }

    /**
     * set request url
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->_currentUrl = $url;
        $this->setOpt(CURLOPT_URL,$url);
    }

    /**
     * get curl handle source
     * @return resource
     */
    public function getCurlHandle()
    {
        return $this->_curlHandle;
    }
}
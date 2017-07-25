<?php
/*
         M""""""""`M            dP                     
         Mmmmmm   .M            88                     
         MMMMP  .MMM  dP    dP  88  .dP   .d8888b.     
         MMP  .MMMMM  88    88  88888"    88'  `88     
         M' .MMMMMMM  88.  .88  88  `8b.  88.  .88     
         M         M  `88888P'  dP   `YP  `88888P'     
         MMMMMMMMMMM  [    -*- Magebay.com -*-   ]      
                                                       
         * * * * * * * * * * * * * * * * * * * * *     
         * -    - -    M.A.G.E.B.A.Y    - -    - *     
         * -  Copyright © 2010 - 2017 Magebay  - *     
         *    -  -  All Rights Reserved  -  -    *     
         * * * * * * * * * * * * * * * * * * * * *     
                                                     
*//**
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
 * @PROJECT    : PDP - M2 Connector
 * @AUTHOR     : Zuko
 * @COPYRIGHT  : © 2017 Magebay - Magento Ext Provider
 * @LINK       : https://www.magebay.com/
 * @FILE       : CorsResponseHelper.php
 * @CREATED    : 10:18 AM , 25/Jul/2017
 * @VERSION    : v2.0.3
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
**/


namespace PDP\Integration\Helper;

/**
 * Class CorsResponseHelper
 * @package PDP\Integration\Helper
 * @since 2.0.3
 */
class CorsResponseHelper {

    const ALLOW_ORIGIN_DOMAIN = '*';
    const ALLOW_REQUEST_METHODS = ['POST','PUT','DELETE','GET','OPTIONS'];

    /**
     * Magento Http Reponse Instance
     * @var \Magento\Framework\Webapi\Response
     * @since 2.0.3
     */
    private $_response;

    /**
     * Value to set on "Access-Control-Allow-Origin" field.
     *
     * @var string
     */
    private $_allowOrigin;

    /**
     * @param string $allowOrigin
     * @return CorsResponseHelper
     */
    public function setAllowOrigin($allowOrigin)
    {
        $this->_allowOrigin = $allowOrigin;

        return $this;
    }

    /**
     * @param array|string $allowMethods
     * @return CorsResponseHelper
     */
    public function setAllowMethods($allowMethods)
    {
        $this->_allowMethods = $allowMethods;

        return $this;
    }

    /**
     * Methods to set on Header "Access-Control-Allow-Methods" field
     *
     * @var array|string
     */
    private $_allowMethods;

    /**
     * @return string
     */
    public function getAllowOrigin()
    {
        if(!$this->_allowOrigin)
            $this->setAllowOrigin(self::ALLOW_ORIGIN_DOMAIN);
        return $this->_allowOrigin;
    }

    /**
     * @return array|string
     */
    public function getAllowMethods()
    {
        if(!$this->_allowMethods)
            $this->setAllowMethods(self::ALLOW_REQUEST_METHODS);
        return $this->_allowMethods;
    }

    /**
     * @param \Magento\Framework\App\ResponseInterface
     * @return CorsResponseHelper
     * @since 2.0.3
     */
    public function setResponse($response)
    {
        $this->_response = $response;

        return $this;
    }

    /**
     * Add HTTP access control (CORS) Header fields to Response Object
     *
     * @param \Magento\Framework\Webapi\Response $response
     * @return \Magento\Framework\Webapi\Response
     * @throws \Exception
     * @since 2.0.3
     */
    public function addCorsHeaders(\Magento\Framework\Webapi\Response $response)
    {
        if(!$response)
            throw new \Exception('$response must be an instance of \Magento\Framework\Webapi\Response . ' . get_class($response) . 'given');

        $this->setResponse($response);
        $this->_response->setHeader('Access-Control-Allow-Origin', $this->getAllowOrigin(),true);
        $this->_response->setHeader('Access-Control-Allow-Credentials', 'true',true);
        if($this->getAllowMethods())
        {
            if(is_array($this->getAllowMethods()))
            {
                $methods = $this->getAllowMethods();
                $accessMethods = array_shift($methods);
                foreach ($methods as $ALLOW_REQUEST_METHOD)
                {
                    $accessMethods .= ', ' . strtoupper($ALLOW_REQUEST_METHOD);
                }
            }
            if($accessMethods) $this->_response->setHeader('Access-Control-Allow-Methods', $accessMethods,true);
        }
        $this->_response->setHeader('Access-Control-Allow-Headers', 'Content-Type, User-Agent',true);
        $this->_response->clearHeader('X-Frame-Options');
        return $this->_response;
    }

    /**
     * Remove Header "X-Frame-Options" (avoid error on Firefox)
     *
     * @param \Magento\Framework\Webapi\Response $response
     * @return \Magento\Framework\Webapi\Response
     * @throws \Exception
     * @since 2.0.3
     */
    public function removeXFrameOptions(\Magento\Framework\Webapi\Response $response)
    {
        if(!$response)
            throw new \Exception('$response must be an instance of \Magento\Framework\Webapi\Response . ' . get_class($response) . 'given');

        $this->setResponse($response);
        $headers = $this->_response->getHeaders();
        $headers->addHeaderLine('X-Frame-Options: ALLOW-FROM *');
        if($headers->has('X-Frame-Options'))
        {
            $headers->removeHeader($headers->get('X-Frame-Options'));
        }
        $this->_response->setHeaders($headers);
        return $this->_response;
    }
}
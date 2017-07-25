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
 * @FILE       : CorsHeadersPlugin.php
 * @CREATED    : 1:54 PM , 24/Jul/2017
 * @VERSION    : v2.0.3
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
**/


namespace PDP\Integration\Plugin;


use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Webapi\Rest\Response;
use PDP\Integration\Helper\CorsResponseHelper;

/**
 * Class CorsHeadersPlugin
 * Add a "Access-Control-Allow-Origin" Header to PDP-Integration api URLs
 *
 * @package PDP\Integration\Plugin
 * @since 2.0.3
 */
class CorsHeadersPlugin {



    /**
     * Rest API Magento Reponse Instance
     * @var \Magento\Framework\Webapi\Rest\Response
     * @since 2.0.3
     */
    private $_response;

    /**
     * CORS Response Helper . Add Headers to Response Object
     *
     * @var \PDP\Integration\Helper\CorsResponseHelper
     * @since 2.0.3
     */
    private $_corsResponseHelper;

    /**
     * CorsHeadersPlugin constructor.
     *
     * @param \Magento\Framework\Webapi\Rest\Response    $response
     * @param \PDP\Integration\Helper\CorsResponseHelper $corsResponseHelper
     * @since 2.0.3
     */
    public function __construct(Response $response,CorsResponseHelper $corsResponseHelper)
    {
        $this->_response = $response;
        $this->_corsResponseHelper = $corsResponseHelper;
    }

    /**
     * Triggers before original dispatch
     * This method triggers before original \Magento\Webapi\Controller\Rest::dispatch and set version
     * from request params to VersionManager instance
     * @param FrontControllerInterface $subject
     * @param RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @since 2.0.3
     */
    public function beforeDispatch(
        FrontControllerInterface $subject,
        RequestInterface $request
    )
    {
        if($request->getHeader('Origin'))
        {
            $this->_corsResponseHelper->setAllowOrigin($request->getHeader('Origin'));
        }
        $this->_response = $this->_corsResponseHelper->addCorsHeaders($this->_response);
    }
}
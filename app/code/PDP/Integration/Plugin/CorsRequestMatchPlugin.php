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
 * @FILE       : CorsRequestMatchPlugin.php
 * @CREATED    : 4:23 PM , 24/Jul/2017
 * @VERSION    : v2.0.3
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
**/


namespace PDP\Integration\Plugin;


use Magento\Framework\Webapi\Request;
use Magento\Webapi\Controller\Rest\Router;

/**
 * Class CorsRequestMatchPlugin
 * @package PDP\Integration\Plugin
 * @since 2.0.3
 */
class CorsRequestMatchPlugin {

    /**
     * @var \Magento\Framework\Webapi\Rest\Request
     * @since 2.0.3
     */
    private $request;
    /**
     * @var \Magento\Framework\Controller\Router\Route\Factory
     * @since 2.0.3
     */
    protected $routeFactory;
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Framework\Controller\Router\Route\Factory $routeFactory\
     * @since 2.0.3
     */
    public function __construct(
        \Magento\Framework\Webapi\Rest\Request $request,
        \Magento\Framework\Controller\Router\Route\Factory $routeFactory
    ) {
        $this->request = $request;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Generate the list of available REST routes. Current HTTP method is taken into account.
     *
     * @param \Magento\Webapi\Controller\Rest\Router|\Magento\Webapi\Model\Rest\Config $subject
     * @param callable                                                                 $proceed
     * @param Request                                                                  $request
     * @return \Magento\Webapi\Controller\Rest\Router\Route
     * @throws \Exception
     * @since 2.0.3
     */
    public function aroundMatch(
        Router $subject,
        callable $proceed,
        Request $request
    )
    {
        try {
            $returnValue = $proceed($request);
        } catch (\Exception $e) {
//            $request->setMethod('OPTIONS');
            $requestHttpMethod = $request->getHttpMethod();
            if ($requestHttpMethod == 'OPTIONS') {
                return $this->createRoute();
            } else {
                throw $e;
            }
        }
        return $returnValue;
    }
    /**
     * Create route object to the placeholder CORS route.
     *
     * @return \Magento\Webapi\Controller\Rest\Router\Route
     * @since 2.0.3
     */
    protected function createRoute()
    {
        /** @var $route \Magento\Webapi\Controller\Rest\Router\Route */
        $route = $this->routeFactory->createRoute(
            'Magento\Webapi\Controller\Rest\Router\Route',
            '/V1/pdpintegration/add'
        );
        $route->setServiceClass('PDP\Integration\Api\PdpGuestDesignRepositoryInterface')
              ->setServiceMethod('checkCORS')
              ->setSecure(false)
              ->setAclResources(['anonymous'])
              ->setParameters([]);
        return $route;
    }
}
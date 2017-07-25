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
 * @FILE       : CorsRequestOptionsPlugin.php
 * @CREATED    : 4:21 PM , 24/Jul/2017
 * @VERSION    : v2.0.3
 * --*--*--*--*--*--*--*--*--*--*--*--*--*--*--*--*-- *
**/


namespace PDP\Integration\Plugin;


use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CorsRequestOptionsPlugin
 * @package PDP\Integration\Plugin
 * @since 2.0.3
 */
class CorsRequestOptionsPlugin {
    /**
     * Triggers before original dispatch
     * Allow Options requests from jQuery AJAX
     *
     * @param Request $subject
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @since 2.0.3
     */
    public function aroundGetHttpMethod(
        Request $subject
    ) {
        if (!$subject->isGet() && !$subject->isPost() && !$subject->isPut() && !$subject->isDelete() && !$subject->isOptions()) {
            throw new \Magento\Framework\Exception\InputException(new Phrase('Request method is invalid.'));
        }
        return $subject->getMethod();
    }

}
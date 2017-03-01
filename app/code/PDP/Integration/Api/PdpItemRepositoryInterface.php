<?php

namespace PDP\Integration\Api;

/**
 * Interface for managing guest shipping address information
 * @api
 */
interface PdpItemRepositoryInterface {
    
	/**
     * Performs persist operations for a specified pdpproduct.
     *
     * @param \PDP\Integration\Api\Data\PdpItemInterface $pdpItem .
     * @return \PDP\Integration\Api\Data\PdpReponseInterface
     */
    public function save(\PDP\Integration\Api\Data\PdpItemInterface $pdpItem);
}
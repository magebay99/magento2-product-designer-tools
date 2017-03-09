<?php
namespace PDP\Integration\Api;

/**
 * Interface for managing pdp my custom design information
 * @api
 */
interface PdpGuestDesignRepositoryInterface {
	
	/**
	 * @param \PDP\Integration\Api\Data\PdpDesignItemInterface $pdpDesignItem .
	 * @return \PDP\Integration\Api\Data\PdpReponseInterface
	 */
	public function save(\PDP\Integration\Api\Data\PdpDesignItemInterface $pdpDesignItem);
} 
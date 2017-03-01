<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface PdpReponseInterface extends ExtensibleDataInterface {
	
	const KEY_URL = 'url';
	
	const KEY_STATUS = 'status';
	
	const KEY_MESSAGE = 'message';
	
    /**
     * Returns the url.
     *
     * @return string|null url. Otherwise, null.
     */
    public function getUrl();

    /**
     * Sets the url.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);
	
    /**
     * Returns the status .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getStatus();

    /**
     * Sets the status.
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status);
	
    /**
     * Returns the message.
     *
     * @return string|null message. Otherwise, null.
     */
    public function getMessage();

    /**
     * Sets the message.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message);	
}
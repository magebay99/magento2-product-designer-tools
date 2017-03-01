<?php
namespace PDP\Integration\Model\Pdpproduct;

use PDP\Integration\Api\Data\PdpReponseInterface;
use Magento\Framework\Api\AbstractExtensibleObject;


class PdpReponse extends AbstractExtensibleObject implements PdpReponseInterface {
	
    /**
     * Returns the url.
     *
     * @return string|null url. Otherwise, null.
     */
    public function getUrl() {
		return $this->_get(self::KEY_URL);
	}
	
    /**
     * Sets the url.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url) {
		return $this->setData(self::KEY_URL, $url);
	}
	
    /**
     * Returns the status .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getStatus() {
		return $this->_get(self::KEY_STATUS);
	}
	
    /**
     * Sets the status.
     *
     * @param bool $status
     * @return $this
     */
    public function setStatus($status) {
		return $this->setData(self::KEY_STATUS, $status);
	}
	
    /**
     * Returns the message.
     *
     * @return string|null message. Otherwise, null.
     */
    public function getMessage() {
		return $this->_get(self::KEY_MESSAGE);
	}
	
    /**
     * Sets the message.
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message) {
		return $this->setData(self::KEY_MESSAGE, $message);
	}	
}
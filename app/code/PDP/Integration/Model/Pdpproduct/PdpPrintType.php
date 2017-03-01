<?php
namespace PDP\Integration\Model\Pdpproduct;

use PDP\Integration\Api\Data\PdpPrintTypeInterface;
use Magento\Framework\Api\AbstractExtensibleObject;


class PdpPrintType extends AbstractExtensibleObject implements PdpPrintTypeInterface {
    
	/**
     * Returns the print type title.
     *
     * @return string|null title. Otherwise, null.
     */
    public function getTitle() {
		return $this->_get(self::KEY_TITLE);
	}
	
    /**
     * Sets the print type title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title) {
		return $this->setData(self::KEY_TITLE, $title);
	}
	
	/**
     * Returns the print type value.
     *
     * @return int|null print type value. Otherwise, null.
     */
    public function getValue() {
		return $this->_get(self::KEY_VALUE);
	}
	
    /**
     * Sets the print type value.
     *
     * @param int $value
     * @return $this
     */
    public function setValue($value) {
		return $this->setData(self::KEY_VALUE, $value);
	}

    /**
     * Returns the print type price.
     *
     * @return float|null print type price. Otherwise, null.
     */
    public function getPrice() {
		return $this->_get(self::KEY_PRICE);
	}

    /**
     * Sets the print type price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
		return $this->setData(self::KEY_PRICE, $price);
	}	
}
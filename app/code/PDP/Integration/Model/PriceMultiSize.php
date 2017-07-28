<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\PriceMultiSizeInterface;

class PriceMultiSize extends AbstractExtensibleObject implements PriceMultiSizeInterface {
	
    /**
     * Returns the size.
     *
     * @return string|null size. Otherwise, null.
     */
	public function getSize() {
		return $this->_get(self::KEY_SIZE);
	}
	
    /**
     * Sets the size.
     *
     * @param string $size
     * @return $this
     */
	public function setSize($size) {
		return $this->setData(self::KEY_SIZE, $size);
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
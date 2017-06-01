<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\ProductColorInterface;

class ProductColor extends AbstractExtensibleObject implements ProductColorInterface {
	
    /**
     * Gets the color ID.
     *
     * @return int|null color ID.
     */
    public function getColorId() {
		return $this->_get(self::COLOR_ID);
	}

    /**
     * Sets color ID.
     *
     * @param int $colorId
     * @return $this
     */
    public function setColorId($colorId) {
		return $this->setData(self::COLOR_ID, $colorId);
	}
	
    /**
     * Returns the color code.
     *
     * @return string|null color code. Otherwise, null.
     */
    public function getColorCode() {
		return $this->_get(self::COLOR_CODE);
	}

    /**
     * Sets the color code.
     *
     * @param string $colorCode
     * @return $this
     */
    public function setColorCode($colorCode) {
		return $this->setData(self::COLOR_CODE, $colorCode);
	}
	
    /**
     * Returns the color price.
     *
     * @return float|null color price. Otherwise, null.
     */
    public function getColorPrice() {
		return $this->_get(self::COLOR_PRICE);
	}
	
    /**
     * Sets the color price.
     *
     * @param string $colorPrice
     * @return $this
     */
    public function setColorPrice($colorPrice) {
		return $this->setData(self::COLOR_PRICE, $colorPrice);
	}
	
    /**
     * Returns the color name.
     *
     * @return string|null color name. Otherwise, null.
     */
    public function getColorName() {
		return $this->_get(self::COLOR_NAME);
	}

    /**
     * Sets the color name.
     *
     * @param string $colorName
     * @return $this
     */
    public function setColorName($colorName) {
		return $this->setData(self::COLOR_NAME, $colorName);
	}
}
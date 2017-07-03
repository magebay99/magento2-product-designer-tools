<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\CustomSizeInterface;

class CustomSize extends AbstractExtensibleObject implements CustomSizeInterface {

	/**
	 * Gets size layout
	 *
	 * @return string|null size layout. Otherwise, null.
	 */
	public function getSizeLayout() {
		return $this->_get(self::SIZE_LAYOUT);
	}
	
	/**
	 * Sets size layout
	 *
	 * @param string $sizeLayout
	 * @return $this
	 */
	public function setSizeLayout($sizeLayout) {
		return $this->setData(self::SIZE_LAYOUT, $sizeLayout);
	}
	
	/**
	 * Gets size height
	 *
	 * @return int|null height. Otherwise, null.
	 */
	public function getHeight() {
		return $this->_get(self::HEIGHT);
	}
	
	/**
	 * Sets size height
	 *
	 * @param int $height
	 * @return $this
	 */
	public function setHeight($height) {
		return $this->setData(self::HEIGHT, $height);
	}
	
	/**
	 * Gets size width
	 *
	 * @return int|null width. Otherwise, null.
	 */
	public function getWidth() {
		return $this->_get(self::WDTH);
	}
	
	/**
	 * Sets size height
	 *
	 * @param int $width
	 * @return $this
	 */
	public function setWidth($width) {
		return $this->setData(self::WDTH, $width);
	}
	
	/**
	 * Gets size unit
	 *
	 * @return string|null size unit. Otherwise, null.
	 */
	public function getUnit() {
		return $this->_get(self::UNIT);
	}
	
	/**
	 * Sets size unit
	 *
	 * @param int $unit
	 * @return $this
	 */
	public function setUnit($unit) {
		return $this->setData(self::UNIT, $unit);
	}
}
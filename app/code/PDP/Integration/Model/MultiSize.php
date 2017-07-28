<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\MultiSizeInterface;

class MultiSize extends AbstractExtensibleObject implements MultiSizeInterface {
	
	/**
	 * Return the name 
	 *
	 * @return string|null name. Otherwise, null.
	 */
	public function getName() {
		return $this->_get(self::KEY_NAME);
	}
	
	/**
	 * Sets name
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name) {
		return $this->setData(self::KEY_NAME, $name);
	}
	
    /**
     * Returns the num.
     *
     * @return int|null num. Otherwise, null.
     */
    public function getNum() {
		return $this->_get(self::KEY_NUM);
	}

    /**
     * Sets the num.
     *
     * @param int $num
     * @return $this
     */
    public function setNum($num) {
		return $this->setData(self::KEY_NUM, $num);
	}
	
	/**
	 * Return the size 
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
     * Returns the size price.
     *
     * @return float|null size price. Otherwise, null.
     */
    public function getPrice() {
		return $this->_get(self::KEY_PRICE);
	}

    /**
     * Sets the size price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
		return $this->setData(self::KEY_PRICE, $price);
	}
	
    /**
     * Gets the qty.
     *
     * @return int|null qty.
     */
    public function getQty() {
		return $this->_get(self::KEY_QTY);
	}

    /**
     * Sets qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty) {
		return $this->setData(self::KEY_QTY, $qty);
	}
		
}
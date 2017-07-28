<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface MultiSizeInterface extends ExtensibleDataInterface {
	
	/**
	 * Name
	 */
	const KEY_NAME = 'name';
	
	/**
	 * Number
	 */
	const KEY_NUM = 'num';
	
	/**
	 * Size
	 */
	const KEY_SIZE = 'size';
	
	/**
	 * Price
	 */
	const KEY_PRICE = 'price';
	
	/**
	 * Qty
	 */
	const KEY_QTY = 'qty';
	
	/**
	 * Return the name 
	 *
	 * @return string|null name. Otherwise, null.
	 */
	public function getName();
	
	/**
     * Sets the name.
     *
     * @param string $name
     * @return $this
	 */
	public function setName($name);
	
    /**
     * Returns the num.
     *
     * @return int|null num. Otherwise, null.
     */
    public function getNum();

    /**
     * Sets the num.
     *
     * @param int $num
     * @return $this
     */
    public function setNum($num);
	
	/**
	 * Return the size 
	 *
	 * @return string|null size. Otherwise, null.
	 */
	public function getSize();
	
	/**
     * Sets the size.
     *
     * @param string $size
     * @return $this
	 */
	public function setSize($size);	
	
    /**
     * Returns the size price.
     *
     * @return float|null size price. Otherwise, null.
     */
    public function getPrice();

    /**
     * Sets the size price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);
	
    /**
     * Gets the qty.
     *
     * @return int|null qty.
     */
    public function getQty();

    /**
     * Sets qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);	
}
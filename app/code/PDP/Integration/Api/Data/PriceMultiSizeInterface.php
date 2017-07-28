<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface PriceMultiSizeInterface extends ExtensibleDataInterface {
	
	/**
	 * Size
	 */
	const KEY_SIZE = 'size';
	
	/**
	 * Price print type
	 */
	const KEY_PRICE = 'price';
	
    /**
     * Returns the size.
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
     * Returns the print type price.
     *
     * @return float|null print type price. Otherwise, null.
     */
    public function getPrice();

    /**
     * Sets the print type price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);
}
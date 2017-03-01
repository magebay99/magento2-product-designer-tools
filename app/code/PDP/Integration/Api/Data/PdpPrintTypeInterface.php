<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface PdpPrintTypeInterface extends ExtensibleDataInterface {
		
	const KEY_TITLE = 'title';
	
	const KEY_VALUE = 'value';
	
	const KEY_PRICE = 'price';
	
    /**
     * Returns the print type title.
     *
     * @return string|null title. Otherwise, null.
     */
    public function getTitle();

    /**
     * Sets the print type title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);	

	/**
     * Returns the print type value.
     *
     * @return int|null print type value. Otherwise, null.
     */
    public function getValue();
	
    /**
     * Sets the print type value.
     *
     * @param int $value
     * @return $this
     */
    public function setValue($value);

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
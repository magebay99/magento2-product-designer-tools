<?php
namespace PDP\Integration\Api\Data;

interface ProductColorInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    
	/**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const COLOR_ID = 'color_id';
	
    const COLOR_CODE = 'color_code';
	
    const COLOR_NAME = 'color_name';
	
    /**
     * Gets the color ID.
     *
     * @return int|null color ID.
     */
    public function getColorId();

    /**
     * Sets color ID.
     *
     * @param int $colorId
     * @return $this
     */
    public function setColorId($colorId);
	
    /**
     * Returns the color code.
     *
     * @return string|null color code. Otherwise, null.
     */
    public function getColorCode();

    /**
     * Sets the color code.
     *
     * @param string $colorCode
     * @return $this
     */
    public function setColorCode($colorCode);

    /**
     * Returns the color name.
     *
     * @return string|null color name. Otherwise, null.
     */
    public function getColorName();

    /**
     * Sets the color name.
     *
     * @param string $colorName
     * @return $this
     */
    public function setColorName($colorName);	
}
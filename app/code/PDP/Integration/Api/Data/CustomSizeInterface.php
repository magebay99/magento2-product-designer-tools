<?php
namespace PDP\Integration\Api\Data;

interface CustomSizeInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
	
	const  SIZE_LAYOUT = 'size_layout';
	const  HEIGHT = 'height';
	const  WDTH = 'wdth';
	const  UNIT = 'unit';
	
	/**
	 * Gets size layout
	 *
	 * @return string|null size layout. Otherwise, null.
	 */
	public function getSizeLayout();
	
	/**
	 * Sets size layout
	 *
	 * @param string $sizeLayout
	 * @return $this
	 */
	public function setSizeLayout($sizeLayout);
	
	/**
	 * Gets size height
	 *
	 * @return int|null height. Otherwise, null.
	 */
	public function getHeight();
	
	/**
	 * Sets size height
	 *
	 * @param int $height
	 * @return $this
	 */
	public function setHeight($height);
	
	/**
	 * Gets size width
	 *
	 * @return int|null width. Otherwise, null.
	 */
	public function getWidth();
	
	/**
	 * Sets size height
	 *
	 * @param int $width
	 * @return $this
	 */
	public function setWidth($width);
	
	/**
	 * Gets size unit
	 *
	 * @return string|null size unit. Otherwise, null.
	 */
	public function getUnit();
	
	/**
	 * Sets size unit
	 *
	 * @param int $unit
	 * @return $this
	 */
	public function setUnit($unit);
	
}
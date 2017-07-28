<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\PdpItemInterface;

class PdpItem extends AbstractExtensibleObject implements PdpItemInterface {

    /**
     * Gets the ID for the pdpproduct.
     *
     * @return int|null pdpproduct ID.
     */
    public function getEntityId() {
		return $this->_get(self::ENTITY_ID);
	}
	
    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId) {
		return $this->setData(self::ENTITY_ID, $entityId);
	}

    /**
     * Gets the ID for the item.
     *
     * @return int|null item ID.
     */
    public function getItemId() {
		return $this->_get(self::ITEM_ID);
	}
	
    /**
     * Sets entity ID.
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId) {
		return $this->setData(self::ITEM_ID, $itemId);
	}	
	
	/**
     * Gets the Design ID 
     *
     * @return int|null design ID.
     */
    public function getDesignId() {
		return $this->_get(self::DESIGN_ID);
	}
	
    /**
     * Sets design ID.
     *
     * @param int $designId
     * @return $this
     */
    public function setDesignId($designId) {
		return $this->setData(self::DESIGN_ID, $designId);
	}	
	
    /**
     * Gets the qty for the pdpproduct.
     *
     * @return int|null pdpproduct qty.
     */
    public function getQty() {
		return $this->_get(self::PDP_QTY);
	}
	
    /**
     * Sets qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty) {
		return $this->setData(self::PDP_QTY, $qty);
	}
	
    /**
     * Returns the sku.
     *
     * @return string|null sku. Otherwise, null.
     */
    public function getSku() {
		return $this->_get(self::PDP_SKU);
	}
	
    /**
     * Sets the sku.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku) {
		return $this->setData(self::PDP_SKU, $sku);
	}
	
    /**
     * Returns the price.
     *
     * @return float|null price. Otherwise, null.
     */
    public function getPrice() {
		return $this->_get(self::PDP_PRICE);
	}
	
    /**
     * Sets the price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
		return $this->setData(self::PDP_PRICE, $price);
	}	

    /**
     * Returns the design_url.
     *
     * @return string|null design_url. Otherwise, null.
     */
    public function getDesignUrl() {
		return $this->_get(self::PDP_URL_PRODUCT);
	}
	
    /**
     * Sets the design_url.
     *
     * @param string $design_url
     * @return $this
     */
    public function setDesignUrl($design_url) {
		return $this->setData(self::PDP_URL_PRODUCT, $design_url);
	}
	
    /**
     * Gets the pdp product color.
     *
     * @return \PDP\Integration\Api\Data\ProductColorInterface|null
     */
    public function getProductColor() {
		return $this->_get(self::PDP_PRODUCT_COLOR);
	}
	
    /**
     * Sets the pdp product color.
	 * @param \PDP\Integration\Api\Data\ProductColorInterface $productColor
     * @return $this
     */
    public function setProductColor(\PDP\Integration\Api\Data\ProductColorInterface $productColor) {
		return $this->setData(self::PDP_PRODUCT_COLOR, $productColor);
	}
	
	/**
	 * Gets the custom size
	 *
	 * @return \PDP\Integration\Api\Data\CustomSizeInterface $customSize|null
	 */
	public function getCustomSize() {
		return $this->_get(self::PDP_CUSTOM_SIZE);
	}
	
	/**
	 * Sets the custom size
	 * @param \PDP\Integration\Api\Data\CustomSizeInterface $customSize
	 * @return this
	 */
	public function setCustomSize(\PDP\Integration\Api\Data\CustomSizeInterface $customSize) {
		return $this->setData(self::PDP_CUSTOM_SIZE, $customSize);
	}

	/**
	 * Gets the pdp multi size
	 * @return \PDP\Integration\Api\Data\MultiSizeInterface[]|null
	 */
	public function getMultiSize() {
		return $this->_get(self::PDP_MULTI_SIZE);
	}
	
	/**
	 * Sets the multi size
	 * @param \PDP\Integration\Api\Data\MultiSizeInterface[] $multiSize
	 * @return $this
	 */
	public function setMultiSize($multiSize) {
		return $this->setData(self::PDP_MULTI_SIZE, $multiSize);
	}
	
    /**
     * Gets the pdp options.
     *
     * @return \PDP\Integration\Api\Data\ProductOptionInterface[]|null
     */
    public function getPdpOptions() {
		return $this->_get(self::PDP_OPTIONS);
	}
	
    /**
     * Sets the pdp options.
     * @param \PDP\Integration\Api\Data\ProductOptionInterface[] $pdpOptions
     */
    public function setPdpOptions( $pdpOptions) {
		return $this->setData(self::PDP_OPTIONS, $pdpOptions);
	}
	
    /**
     * Gets the pdp print type.
     *
     * @return \PDP\Integration\Api\Data\PdpPrintTypeInterface|null
     */
    public function getPdpPrintType() {
		return $this->_get(self::PDP_PRINT_TYPE);
	}

    /**
     * Sets the pdp options.
	 * @param \PDP\Integration\Api\Data\PdpPrintTypeInterface $pdpPrintType
     * @return $this
     */
    public function setPdpPrintType(\PDP\Integration\Api\Data\PdpPrintTypeInterface $pdpPrintType) {
		return $this->setData(self::PDP_PRINT_TYPE, $pdpPrintType);
	}	
}
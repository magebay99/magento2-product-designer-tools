<?php
namespace PDP\Integration\Api\Data;

interface PdpDesignItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface {

    const DESIGN_ID = 'design_id';
	
    const PRODUCT_ID = 'product_id';
	
    const PRODUCT_SKU = 'product_sku';
	
    /**
     * Gets the Design ID.
     *
     * @return int|null design ID.
     */
    public function getDesignId();	
	
    /**
     * Sets Design ID.
     *
     * @param int $designId
     * @return $this
     */
    public function setDesignId($designId);
	
    /**
     * Gets the product ID for the item.
     *
     * @return int|null product ID.
     */
    public function getProductId();

    /**
     * Sets product ID.
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);
	
    /**
     * Returns the product sku.
     *
     * @return string|null product sku. Otherwise, null.
     */
    public function getProductSku();

    /**
     * Sets the product sku.
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku);	
	
}
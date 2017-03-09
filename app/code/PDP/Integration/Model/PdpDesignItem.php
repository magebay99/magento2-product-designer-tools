<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\Data\PdpDesignItemInterface;

class PdpDesignItem extends AbstractExtensibleObject implements PdpDesignItemInterface {

    /**
     * Gets the Design ID.
     *
     * @return int|null design ID.
     */
    public function getDesignId() {
		return $this->_get(self::DESIGN_ID);
	}
	
    /**
     * Sets Design ID.
     *
     * @param int $designId
     * @return $this
     */
    public function setDesignId($designId) {
		return $this->setData(self::DESIGN_ID, $designId);
	}
	
    /**
     * Gets the product ID for the item.
     *
     * @return int|null product ID.
     */
    public function getProductId() {
		return $this->_get(self::PRODUCT_ID);
	}

    /**
     * Sets product ID.
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId) {
		return $this->setData(self::PRODUCT_ID, $productId);
	}
	
    /**
     * Returns the product sku.
     *
     * @return string|null product sku. Otherwise, null.
     */
    public function getProductSku() {
		return $this->_get(self::PRODUCT_SKU);
	}

    /**
     * Sets the product sku.
     *
     * @param string $productSku
     * @return $this
     */
    public function setProductSku($productSku) {
		return $this->setData(self::PRODUCT_SKU, $productSku);
	}
}
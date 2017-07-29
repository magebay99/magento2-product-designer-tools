<?php

namespace PDP\Integration\Api\Data;

interface PdpItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface {
    
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    const ENTITY_ID = 'entity_id';
	
    const ITEM_ID = 'item_id';
	
    const DESIGN_ID = 'design_id';
	
    const PDP_QTY = 'qty';
	
    const PDP_SKU = 'sku';
	
    const PDP_PRICE = 'price';
	
    const PDP_URL_PRODUCT = 'design_url';
	
    const PDP_PRODUCT_COLOR = 'product_color';
	
	/*
	 * pdp custom size
	 */
	const PDP_CUSTOM_SIZE = 'custom_size';
	
	/**
	 * pdp Multi size
	 */
	const PDP_MULTI_SIZE = 'multi_size';
	
	/*
     * pdp option.
     */
    const PDP_OPTIONS = 'pdp_options';
	
    const PDP_PRINT_TYPE = 'pdp_print_type';
	
    /**
     * Gets the ID for the pdpproduct.
     *
     * @return int|null pdpproduct ID.
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);
	
    /**
     * Gets the ID for the item.
     *
     * @return int|null item ID.
     */
    public function getItemId();

    /**
     * Sets entity ID.
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);
	
	/**
     * Gets the Design ID 
     *
     * @return int|null design ID.
     */
    public function getDesignId();

    /**
     * Sets design ID.
     *
     * @param int $designId
     * @return $this
     */
    public function setDesignId($designId);	
	
    /**
     * Gets the qty for the pdpproduct.
     *
     * @return int|null pdpproduct qty.
     */
    public function getQty();

    /**
     * Sets qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);
	
	
    /**
     * Returns the sku.
     *
     * @return string|null sku. Otherwise, null.
     */
    public function getSku();

    /**
     * Sets the sku.
     *
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);
	
    /**
     * Returns the price.
     *
     * @return float|null price. Otherwise, null.
     */
    public function getPrice();

    /**
     * Sets the price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);
	
    /**
     * Returns the design_url.
     *
     * @return string|null design_url. Otherwise, null.
     */
    public function getDesignUrl();

    /**
     * Sets the design_url.
     *
     * @param string $design_url
     * @return $this
     */
    public function setDesignUrl($design_url);	
		
    /**
     * Gets the pdp product color.
     *
     * @return \PDP\Integration\Api\Data\ProductColorInterface|null
     */
    public function getProductColor();
	
    /**
     * Sets the pdp product color.
	 * @param \PDP\Integration\Api\Data\ProductColorInterface $productColor
     * @return $this
     */
    public function setProductColor(\PDP\Integration\Api\Data\ProductColorInterface $productColor);
	
	/**
	 * Gets the pdp custom size
	 * @return \PDP\Integration\Api\Data\CustomSizeInterface $customSize|null
	 */
	public function getCustomSize();
	
	/**
	 * Sets the custom size
	 * @param \PDP\Integration\Api\Data\CustomSizeInterface $customSize
	 * @return $this
	 */
	public function setCustomSize(\PDP\Integration\Api\Data\CustomSizeInterface $customSize);
	
	/**
	 * Gets the pdp multi size
	 * @return \PDP\Integration\Api\Data\MultiSizeInterface[]|null
	 */
	public function getMultiSize();
	
	/**
	 * Sets the multi size
	 * @param \PDP\Integration\Api\Data\MultiSizeInterface[] $multiSize
	 * @return $this
	 */
	public function setMultiSize($multiSize);

    /**
     * Gets the pdp options.
     *
     * @return \PDP\Integration\Api\Data\ProductOptionInterface[]|null
     */
    public function getPdpOptions();
	
    /**
     * Sets the pdp options.
	 * @param \PDP\Integration\Api\Data\ProductOptionInterface[] $pdpOptions
     * @return $this
     */
    public function setPdpOptions($pdpOptions);
	
    /**
     * Gets the pdp print type.
     *
     * @return \PDP\Integration\Api\Data\PdpPrintTypeInterface|null
     */
    public function getPdpPrintType();
	
    /**
     * Sets the pdp options.
	 * @param \PDP\Integration\Api\Data\PdpPrintTypeInterface $pdpPrintType
     * @return $this
     */
    public function setPdpPrintType(\PDP\Integration\Api\Data\PdpPrintTypeInterface $pdpPrintType);	
}
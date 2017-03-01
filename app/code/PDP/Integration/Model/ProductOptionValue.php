<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\data\ProductOptionValueInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class ProductOptionValue extends AbstractExtensibleObject implements ProductOptionValueInterface
{
    /**
     * Returns the option option_type_id.
     *
     * @return int|null option option_type_id. Otherwise, null.
     */
    public function getOptionTypeId() {
		return $this->_get(self::KEY_OPTION_TYPE_ID);
	}
	
    /**
     * Sets the option option_type_id.
     *
     * @param int $option_type_id
     * @return $this
     */
    public function setOptionTypeId($option_type_id) {
		return $this->setData(self::KEY_OPTION_TYPE_ID, $option_type_id);
	}
	
    /**
     * Returns the disable of the option .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisabled() {
		return $this->_get(self::KEY_DISABLED);
	}
	
    /**
     * Sets the disable of the option.
     *
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled) {
		return $this->setData(self::KEY_DISABLED, $disabled);
	}
	
	/**
     * Returns the option qty.
     *
     * @return int|null option qty. Otherwise, null.
     */
    public function getQty() {
		return $this->_get(self::KEY_QTY);
	}
	
    /**
     * Sets the option qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty) {
		return $this->setData(self::KEY_QTY, $qty);
	}
	
    /**
     * Returns the selected of the option .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getSelected() {
		return $this->_get(self::KEY_SELECTED);
	}
	
    /**
     * Sets the selected of the option.
     *
     * @param bool $selected
     * @return $this
     */
    public function setSelected($selected) {
		return $this->setData(self::KEY_SELECTED, $selected);
	}
	
	/**
     * Returns the option in_group_id.
     *
     * @return int|null option in_group_id. Otherwise, null.
     */
    public function getInGroupId() {
		return $this->_get(self::KEY_IN_GROUP_ID);
	}
	
    /**
     * Sets the option in_group_id.
     *
     * @param int $in_group_id
     * @return $this
     */
    public function setInGroupId($in_group_id) {
		return $this->setData(self::KEY_IN_GROUP_ID, $in_group_id);
	}
	
    /**
     * Returns the option title.
     *
     * @return string|null option title. Otherwise, null.
     */
    public function getTitle() {
		return $this->_get(self::KEY_TITLE);
	}
	
    /**
     * Sets the option title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title) {
		return $this->setData(self::KEY_TITLE, $title);
	}
	
    /**
     * Returns the product price.
     *
     * @return float|null Product price. Otherwise, null.
     */
    public function getPrice() {
		return $this->_get(self::KEY_PRICE);
	}
	
    /**
     * Sets the product price.
     *
     * @param float $price
     * @return $this
     */	
	public function setPrice($price) {
		return $this->setData(self::KEY_PRICE, $price);
	}
	
    /**
     * Returns the option price_type.
     *
     * @return string|null option price_type. Otherwise, null.
     */
    public function getPriceType() {
		return $this->_get(self::KEY_PRICE_TYPE);
	}
	
    /**
     * Sets the option price_type.
     *
     * @param string $price_type
     * @return $this
     */
    public function setPriceType($price_type) {
		return $this->setData(self::KEY_PRICE_TYPE, $price_type);
	}
	
	/**
     * Returns the option checked.
     *
     * @return int|null option checked. Otherwise, null.
     */
    public function getChecked() {
		return $this->_get(self::KEY_CHECKED);
	}
	
    /**
     * Sets the option checked.
     *
     * @param int $checked
     * @return $this
     */
    public function setChecked($checked) {
		return $this->setData(self::KEY_CHECKED, $checked);
	}
	
	/**
     * Returns the option sort_order.
     *
     * @return int|null option sort_order. Otherwise, null.
     */
    public function getSortOrder() {
		return $this->_get(self::KEY_SORT_ORDER);
	}
	
    /**
     * Sets the option sort_order.
     *
     * @param int $sort_order
     * @return $this
     */
    public function setSortOrder($sort_order) {
		return $this->setData(self::KEY_SORT_ORDER, $sort_order);
	}
	
    /**
     * Returns the option in_group_id_view.
     *
     * @return string|null option in_group_id_view. Otherwise, null.
     */
    public function getInGroupIdView() {
		return $this->_get(self::KEY_IN_GROUP_ID_VIEW);
	}
	
    /**
     * Sets the option in_group_id_view.
     *
     * @param string $in_group_id_view
     * @return $this
     */
    public function setInGroupIdView($in_group_id_view) {
		return $this->setData(self::KEY_IN_GROUP_ID_VIEW, $in_group_id_view);
	}
	
    /**
     * Returns the option dependent_ids.
     *
     * @return string|null option dependent_ids. Otherwise, null.
     */
    public function getDependentIds() {
		return $this->_get(self::KEY_DEPENDENT_IDS);
	}
	
    /**
     * Sets the option dependent_ids.
     *
     * @param string $dependent_ids
     * @return $this
     */
    public function setDependentIds($dependent_ids) {
		return $this->setData(self::KEY_DEPENDENT_IDS, $dependent_ids);
	}
	
	/**
     * Returns the option customoptions_qty.
     *
     * @return int|null option customoptions_qty. Otherwise, null.
     */
    public function getCustomoptionsQty() {
		return $this->_get(self::KEY_CUSTOMOPTIONS_QTY);
	}
	
    /**
     * Sets the option customoptions_qty.
     *
     * @param int $customoptions_qty
     * @return $this
     */
    public function setCustomoptionsQty($customoptions_qty) {
		return $this->setData(self::KEY_CUSTOMOPTIONS_QTY, $customoptions_qty);
	}	
}
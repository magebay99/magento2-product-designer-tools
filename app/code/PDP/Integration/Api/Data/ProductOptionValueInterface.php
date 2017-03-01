<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Product option interface
 * @api
 */
interface ProductOptionValueInterface extends ExtensibleDataInterface {

    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_OPTION_TYPE_ID = 'option_type_id';	
	
    const KEY_DISABLED = 'disabled';	
	
    const KEY_QTY = 'qty';	
	
    const KEY_SELECTED = 'selected';	
	
    const KEY_IN_GROUP_ID = 'in_group_id';	
	
    const KEY_TITLE = 'title';	
	
    const KEY_PRICE = 'price';
	
    const KEY_PRICE_TYPE = 'price_type';
	
    const KEY_CHECKED = 'checked';
	
    const KEY_SORT_ORDER = 'sort_order';
	
    const KEY_IN_GROUP_ID_VIEW = 'in_group_id_view';
	
    const KEY_DEPENDENT_IDS = 'dependent_ids';
	
    const KEY_CUSTOMOPTIONS_QTY = 'customoptions_qty';
	
    /**
     * Returns the option option_type_id.
     *
     * @return int|null option option_type_id. Otherwise, null.
     */
    public function getOptionTypeId();
	
    /**
     * Sets the option option_type_id.
     *
     * @param int $option_type_id
     * @return $this
     */
    public function setOptionTypeId($option_type_id);
	
    /**
     * Returns the disable of the option .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisabled();

    /**
     * Sets the disable of the option.
     *
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled);

	/**
     * Returns the option qty.
     *
     * @return int|null option qty. Otherwise, null.
     */
    public function getQty();
	
    /**
     * Sets the option qty.
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty);
	
    /**
     * Returns the selected of the option .
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getSelected();

    /**
     * Sets the selected of the option.
     *
     * @param bool $selected
     * @return $this
     */
    public function setSelected($selected);	
	
	/**
     * Returns the option in_group_id.
     *
     * @return int|null option in_group_id. Otherwise, null.
     */
    public function getInGroupId();
	
    /**
     * Sets the option in_group_id.
     *
     * @param int $in_group_id
     * @return $this
     */
    public function setInGroupId($in_group_id);
	
    /**
     * Returns the option title.
     *
     * @return string|null option title. Otherwise, null.
     */
    public function getTitle();

    /**
     * Sets the option title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Returns the product price.
     *
     * @return float|null Product price. Otherwise, null.
     */
    public function getPrice();

    /**
     * Sets the product price.
     *
     * @param float $price
     * @return $this
     */	
	public function setPrice($price);
	
    /**
     * Returns the option price_type.
     *
     * @return string|null option price_type. Otherwise, null.
     */
    public function getPriceType();

    /**
     * Sets the option price_type.
     *
     * @param string $price_type
     * @return $this
     */
    public function setPriceType($price_type);

	/**
     * Returns the option checked.
     *
     * @return int|null option checked. Otherwise, null.
     */
    public function getChecked();
	
    /**
     * Sets the option checked.
     *
     * @param int $checked
     * @return $this
     */
    public function setChecked($checked);
	
	/**
     * Returns the option sort_order.
     *
     * @return int|null option sort_order. Otherwise, null.
     */
    public function getSortOrder();
	
    /**
     * Sets the option sort_order.
     *
     * @param int $sort_order
     * @return $this
     */
    public function setSortOrder($sort_order);

    /**
     * Returns the option in_group_id_view.
     *
     * @return string|null option in_group_id_view. Otherwise, null.
     */
    public function getInGroupIdView();

    /**
     * Sets the option in_group_id_view.
     *
     * @param string $in_group_id_view
     * @return $this
     */
    public function setInGroupIdView($in_group_id_view);
	
    /**
     * Returns the option dependent_ids.
     *
     * @return string|null option dependent_ids. Otherwise, null.
     */
    public function getDependentIds();

    /**
     * Sets the option dependent_ids.
     *
     * @param string $dependent_ids
     * @return $this
     */
    public function setDependentIds($dependent_ids);
	
	/**
     * Returns the option customoptions_qty.
     *
     * @return int|null option customoptions_qty. Otherwise, null.
     */
    public function getCustomoptionsQty();
	
    /**
     * Sets the option customoptions_qty.
     *
     * @param int $customoptions_qty
     * @return $this
     */
    public function setCustomoptionsQty($customoptions_qty);	
}
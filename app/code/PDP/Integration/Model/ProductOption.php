<?php
namespace PDP\Integration\Model;

use Magento\Framework\Api\AbstractExtensibleObject;
use PDP\Integration\Api\data\ProductOptionInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class ProductOption extends AbstractExtensibleObject implements ProductOptionInterface
{
    /**
     * Returns the option ID.
     *
     * @return int|null option ID. Otherwise, null.
     */
    public function getId() {
		return $this->_get(self::KEY_ID);
	}
	
    /**
     * Sets the option ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id) {
		return $this->setData(self::KEY_ID, $id);
	}
	
    /**
     * Sets the option ID.
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId) {
		return $this->setData(self::KEY_OPTION_ID, $optionId);
	}
	
	/**
     * Returns the option ID.
     *
     * @return int|null option ID. Otherwise, null.
     */
    public function getOptionId() {
		return $this->_get(self::KEY_OPTION_ID);
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
     * Returns the option type.
     *
     * @return string|null option type. Otherwise, null.
     */
    public function getType() {
		return $this->_get(self::KEY_TYPE);
	}
	
    /**
     * Sets the option type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type) {
		return $this->setData(self::KEY_TYPE, $type);
	}
	
	/**
     * Returns the option previous_type.
     *
     * @return string|null option previous_type. Otherwise, null.
     */
    public function getPreviousType() {
		return $this->_get(self::KEY_PREVIOUS_TYPE);
	}
	
    /**
     * Sets the option previous_type.
     *
     * @param string $previous_type
     * @return $this
     */
    public function setPreviousType($previous_type) {
		return $this->setData(self::KEY_PREVIOUS_TYPE, $previous_type);
	}
	
    /**
     * Returns the option is_require.
     *
     * @return int|null option is_require. Otherwise, null.
     */
    public function getIsRequire() {
		return $this->_get(self::KEY_IS_REQUIRE);
	}
	
    /**
     * Sets the option is_require.
     *
     * @param int $is_require
     * @return $this
     */
    public function setIsRequire($is_require) {
		return $this->setData(self::KEY_IS_REQUIRE, $is_require);
	}
	
	/**
     * Returns the option is_dependent.
     *
     * @return int|null option is_dependent. Otherwise, null.
     */
    public function getIsDependent() {
		return $this->_get(self::KEY_IS_DEPENDENT);
	}
	
    /**
     * Sets the option is_dependent.
     *
     * @param int $is_dependent
     * @return $this
     */
    public function setIsDependent($is_dependent) {
		return $this->setData(self::KEY_IS_DEPENDENT, $is_dependent);
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
     * Returns the option qnty_input.
     *
     * @return int|null option qnty_input. Otherwise, null.
     */
    public function getQntyInput() {
		return $this->_get(self::KEY_QNTY_INPUT);
	}
	
    /**
     * Sets the option qnty_input.
     *
     * @param int $qnty_input
     * @return $this
     */
    public function setQntyInput($qnty_input) {
		return $this->setData(self::KEY_QNTY_INPUT, $qnty_input);
	}
	
    /**
     * Returns the option qnty_input_disabled.
     *
     * @return string|null option qnty_input_disabled. Otherwise, null.
     */
    public function getQntyInputDisabled() {
		return $this->_get(self::KEY_QNTY_INPUT_DISABLED);
	}
	
    /**
     * Sets the option qnty_input_disabled.
     *
     * @param string $qnty_input_disabled
     * @return $this
     */
    public function setQntyInputDisabled($qnty_input_disabled) {
		return $this->setData(self::KEY_QNTY_INPUT_DISABLED, $qnty_input_disabled);
	}
	
    /**
     * Returns the option price.
     *
     * @return float|null option price. Otherwise, null.
     */
    public function getPrice() {
		return $this->_get(self::KEY_PRICE);
	}
	
    /**
     * Sets the option price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price) {
		return $this->setData(self::KEY_PRICE, $price);
	}
	
	/**
     * Returns the option max_characters.
     *
     * @return int|null option max_characters. Otherwise, null.
     */
    public function getMaxCharacters() {
		return $this->_get(self::KEY_MAX_CHARACTERS);
	}
	
    /**
     * Sets the option max_characters.
     *
     * @param int $max_characters
     * @return $this
     */
    public function setMaxCharacters($max_characters) {
		return $this->setData(self::KEY_MAX_CHARACTERS, $max_characters);
	}
	
    /**
     * Returns the option default_text.
     *
     * @return string|null option default_text. Otherwise, null.
     */
    public function getDefaultText() {
		return $this->_get(self::KEY_DEFAULT_TEXT);
	}
	
    /**
     * Sets the option default_text.
     *
     * @param string $default_text
     * @return $this
     */
    public function setDefaultText($default_text) {
		return $this->setData(self::KEY_DEFAULT_TEXT, $default_text);
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
     * Returns the option file_extension.
     *
     * @return string|null option file_extension. Otherwise, null.
     */
    public function getFileExtension() {
		return $this->_get(self::KEY_FILE_EXTENSION);
	}
	
    /**
     * Sets the option file_extension.
     *
     * @param string $file_extension
     * @return $this
     */
    public function setFileExtension($file_extension) {
		return $this->setData(self::KEY_FILE_EXTENSION, $file_extension);
	}
	
	/**
     * Returns the option image_size_x.
     *
     * @return int|null option image_size_x. Otherwise, null.
     */
    public function getImageSizeX() {
		return $this->_get(self::KEY_IMAGE_SIZE_X);
	}
	
    /**
     * Sets the option image_size_x.
     *
     * @param int $image_size_x
     * @return $this
     */
    public function setImageSizeX($image_size_x) {
		return $this->setData(self::KEY_IMAGE_SIZE_X, $image_size_x);
	}
	
	/**
     * Returns the option image_size_y.
     *
     * @return int|null option image_size_y. Otherwise, null.
     */
    public function getImageSizeY() {
		return $this->_get(self::KEY_IMAGE_SIZE_Y);
	}
	
    /**
     * Sets the option image_size_y.
     *
     * @param int $image_size_y
     * @return $this
     */
    public function setImageSizeY($image_size_y) {
		return $this->setData(self::KEY_IMAGE_SIZE_Y, $image_size_y);
	}
	
    /**
     * Returns the option values.
     *
     * @return \PDP\Integration\Api\Data\ProductOptionValueInterface[] |null
     */
    public function getValues() {
		return $this->_get(self::KEY_VALUES);
	}
	
    /**
     * Sets the option values.
     *
     * @param \PDP\Integration\Api\Data\ProductOptionValueInterface[] $values
     * @return $this
     */
    public function setValues(array $values) {
		return $this->setData(self::KEY_VALUES, $values);
	}
}
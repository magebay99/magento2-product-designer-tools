<?php
namespace PDP\Integration\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Product option interface
 * @api
 */
interface ProductOptionInterface extends ExtensibleDataInterface {
	
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_ID = 'id';
	
    const KEY_OPTION_ID = 'option_id';
	
    const KEY_DISABLED = 'disabled';

    const KEY_TITLE = 'title';

    const KEY_TYPE = 'type';

    const KEY_PREVIOUS_TYPE = 'previous_type';	
	
    const KEY_IS_REQUIRE = 'is_require';
	
    const KEY_IS_DEPENDENT = 'is_dependent';
	
    const KEY_IN_GROUP_ID = 'in_group_id';	
	
    const KEY_IN_GROUP_ID_VIEW = 'in_group_id_view';
	
    const KEY_SORT_ORDER = 'sort_order';	
	
    const KEY_QNTY_INPUT = 'qnty_input';
	
    const KEY_QNTY_INPUT_DISABLED = 'qnty_input_disabled';	
	
    const KEY_VALUES = 'values';	
	
    const KEY_PRICE = 'price';	
	
    const KEY_MAX_CHARACTERS = 'max_characters';	
	
    const KEY_DEFAULT_TEXT = 'default_text';	
	
    const KEY_PRICE_TYPE = 'price_type';	
	
    const KEY_FILE_EXTENSION = 'file_extension';
	
    const KEY_IMAGE_SIZE_X = 'image_size_x';
	
    const KEY_IMAGE_SIZE_Y = 'image_size_y';
	
    /**
     * Returns the option ID.
     *
     * @return int|null option ID. Otherwise, null.
     */
    public function getId();
	
    /**
     * Sets the option ID.
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);
	
    /**
     * Sets the option ID.
     *
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId);	    
	
	/**
     * Returns the option ID.
     *
     * @return int|null option ID. Otherwise, null.
     */
    public function getOptionId();
	
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
     * Returns the option type.
     *
     * @return string|null option type. Otherwise, null.
     */
    public function getType();

    /**
     * Sets the option type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);	    
	
	/**
     * Returns the option previous_type.
     *
     * @return string|null option previous_type. Otherwise, null.
     */
    public function getPreviousType();

    /**
     * Sets the option previous_type.
     *
     * @param string $previous_type
     * @return $this
     */
    public function setPreviousType($previous_type);
	
    /**
     * Returns the option is_require.
     *
     * @return int|null option is_require. Otherwise, null.
     */
    public function getIsRequire();
	
    /**
     * Sets the option is_require.
     *
     * @param int $is_require
     * @return $this
     */
    public function setIsRequire($is_require);	    
	
	/**
     * Returns the option is_dependent.
     *
     * @return int|null option is_dependent. Otherwise, null.
     */
    public function getIsDependent();
	
    /**
     * Sets the option is_dependent.
     *
     * @param int $is_dependent
     * @return $this
     */
    public function setIsDependent($is_dependent);	
	
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
     * Returns the option qnty_input.
     *
     * @return int|null option qnty_input. Otherwise, null.
     */
    public function getQntyInput();
	
    /**
     * Sets the option qnty_input.
     *
     * @param int $qnty_input
     * @return $this
     */
    public function setQntyInput($qnty_input);
	
    /**
     * Returns the option qnty_input_disabled.
     *
     * @return string|null option qnty_input_disabled. Otherwise, null.
     */
    public function getQntyInputDisabled();

    /**
     * Sets the option qnty_input_disabled.
     *
     * @param string $qnty_input_disabled
     * @return $this
     */
    public function setQntyInputDisabled($qnty_input_disabled);
	
    /**
     * Returns the option price.
     *
     * @return float|null option price. Otherwise, null.
     */
    public function getPrice();

    /**
     * Sets the option price.
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price);	
	
	/**
     * Returns the option max_characters.
     *
     * @return int|null option max_characters. Otherwise, null.
     */
    public function getMaxCharacters();
	
    /**
     * Sets the option max_characters.
     *
     * @param int $max_characters
     * @return $this
     */
    public function setMaxCharacters($max_characters);
	
    /**
     * Returns the option default_text.
     *
     * @return string|null option default_text. Otherwise, null.
     */
    public function getDefaultText();

    /**
     * Sets the option default_text.
     *
     * @param string $default_text
     * @return $this
     */
    public function setDefaultText($default_text);
	
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
     * Returns the option file_extension.
     *
     * @return string|null option file_extension. Otherwise, null.
     */
    public function getFileExtension();

    /**
     * Sets the option file_extension.
     *
     * @param string $file_extension
     * @return $this
     */
    public function setFileExtension($file_extension);	

	/**
     * Returns the option image_size_x.
     *
     * @return int|null option image_size_x. Otherwise, null.
     */
    public function getImageSizeX();
	
    /**
     * Sets the option image_size_x.
     *
     * @param int $image_size_x
     * @return $this
     */
    public function setImageSizeX($image_size_x);
	
	/**
     * Returns the option image_size_y.
     *
     * @return int|null option image_size_y. Otherwise, null.
     */
    public function getImageSizeY();
	
    /**
     * Sets the option image_size_y.
     *
     * @param int $image_size_y
     * @return $this
     */
    public function setImageSizeY($image_size_y);		
	
    /**
     * Returns the option values.
     *
     * @return \PDP\Integration\Api\Data\ProductOptionValueInterface[] |null
     */
    public function getValues();

    /**
     * Sets the option values.
     *
     * @param \PDP\Integration\Api\Data\ProductOptionValueInterface[] $values
     * @return $this
     */
    public function setValues(array $values);	
}
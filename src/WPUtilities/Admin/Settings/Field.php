<?php

namespace WPUtilities\Admin\Settings;
 
class Field
{
	/**
	 * Type of field this is
	 * @var string
	 */
	protected $type;

	/**
	 * Machine-friendly name of this field
	 * @var string
	 */
	protected $fieldName;

	/**
	 * Readable name of this field group.
	 * Gets assigned as the field group label.
	 * @var string
	 */
	protected $title;

	/**
	 * Default value of this field
	 * @var string
	 */
	protected $default;

	/**
	 * Definitions of fields are belong
	 * to this field group.
	 * @var array
	 */
	protected $fields = array();

	/**
	 * ID of the options page to assign
	 * this field group to.
	 * @var string
	 */
	protected $page;

	/**
	 * ID of the options section to assign
	 * this field group to.
	 * @var string
	 */
	protected $section;

	/**
	 * Validation function
	 * @var function
	 */
	protected $validation;

	/**
	 * Option ID of this field
	 * @var sting
	 */
	public $option_id;

	/**
	 * WordPress wrapper
	 * @var object
	 */
	protected $wordpress;

	public function __construct($type, $fieldName, $title, $default, $page, $section, $validation = null)
	{
		$this->type = $type;
		$this->fieldName = $fieldName;
		$this->title = $title;
		$this->default = $default;
		$this->page = $page;
		$this->section = $section;
		$this->validation = $validation;

		$this->option_id = "{$this->section}_{$this->fieldName}";

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();

		$this->wordpress->add_action("admin_init", array($this, "addField"));
	}


	public function addField()
	{
		$this->wordpress->add_settings_field(
			$this->option_id,
		    $this->title,
		    array($this, "printField"),
		    $this->page,
		    $this->section,
		    array($this->type, $this->default)  
		);

		$this->wordpress->register_setting($this->page, $this->option_id, $this->validation);
	}

	public function printField($args)
	{
		$type = $args[0];
		$default = $args[1];

		$method = "get_{$type}";
		echo $this->$method($this->option_id, $default);
	}

	protected function get_checkbox($name, $default = null)
	{
		$value = $this->wordpress->get_option($name);
		$checked = $this->wordpress->checked(1, $value, false);
		return FieldCreator::checkbox($name, null, 1, $checked);
	}

	protected function get_text($name, $default = null)
	{
		$value = $this->wordpress->get_option($name);
		return FieldCreator::text($name, null, $value);
	}

}
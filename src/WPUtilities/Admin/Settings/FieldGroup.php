<?php

namespace WPUtilities\Admin\Settings;

class FieldGroup
{
	/**
	 * Machine-friendly name of the field group
	 * @var string
	 */
	protected $groupName;

	/**
	 * Readable name of this field group.
	 * Gets assigned as the field group label.
	 * @var string
	 */
	protected $title;

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
	 * Option ID of this field group
	 * @var sting
	 */
	protected $option_id;

	/**
	 * WordPress wrapper
	 * @var object
	 */
	protected $wordpress;

	public function __construct($groupName, $title, $fields, $page, $section, $validation = null)
	{
		$this->groupName = $groupName;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->section = $section;
		$this->validation = $validation;

		$this->option_id = "{$this->section}_{$this->groupName}";

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();

		$this->wordpress->add_action("admin_init", array($this, "addFieldGroup"));
	}

	public function addFieldGroup()
	{
		$this->wordpress->add_settings_field(
			$this->option_id,
		    $this->title,
		    array($this, "printFields"),
		    $this->page,
		    $this->section,
		    array($this->title, $this->fields)  
		);

		$this->wordpress->register_setting($this->page, $this->option_id, $this->validation);
	}

	public function printFields($args)
	{
		$label = $args[0];
		$subfields = $args[1];

		$html = "";

		foreach ($subfields as $name => $details) {
			$method = "get_{$details['type']}";
			$default = isset($details["default"]) ? $details["default"] : null;

			$html .= $this->$method($this->option_id, $name, $details["label"], $default);
		}

		echo $html;
	}

	/**
	 * Create HTML for a checkbox.
	 * @param  string $optionKey Name of key of WordPress option where this value is saved (as part of an array).
	 * @param  string $key       Name of key to find this form element's specific value
	 * @param  string $label     Element's label
	 * @param  string $default   Element's default value
	 * @return string Checkbox HTML
	 */
	protected function get_checkbox($optionKey, $key, $label, $default = null)
	{
		// Find the current value of this checkbox.
		$value = $this->getOptionValue($optionKey, $key);

		// Create the name attribute of this checkbox
		$name = $this->getFieldName($optionKey, $key);

		$checked = $this->wordpress->checked(1, $value, false);

		return FieldCreator::checkbox($name, $label, 1, $checked);
	}

	/**
	 * Create HTML for a textbox.
	 * @param  string $optionKey Name of key of WordPress option where this value is saved (as part of an array).
	 * @param  string $key       Name of key to find this form element's specific value
	 * @param  string $label     Element's label
	 * @param  string $default   Element's default value
	 * @return string Textbox HTML
	 */
	protected function get_text($optionKey, $key, $label, $default = null)
	{
		// Find the current value of this checkbox.
		$value = $this->getOptionValue($optionKey, $key) || $default;

		// Create the name attribute of this textbox
		$name = $this->getFieldName($optionKey, $key);

		// create html
		return FieldCreator::text($name, $label, $value);
	}

	/**
	 * Find an individual value in a WordPress option that is stored as an array
	 * @param  string $option WordPress option key
	 * @param  string $key    Array key to look for in option value
	 * @return string/null
	 */
	protected function getOptionValue($option, $key)
	{
		$option = $this->wordpress->get_option($option);
		return isset($option[$key]) ? $option[$key] : null;
	}

	/**
	 * Get the name attribute for a form field.
	 * @param  string $option Name of key of WordPress option where this value is saved (as part of an array).
	 * @param  string $key    Name of key to find this form element's specific value
	 * @return string
	 */
	protected function getFieldName($option, $key)
	{
		return $option . "[$key]";
	}
}
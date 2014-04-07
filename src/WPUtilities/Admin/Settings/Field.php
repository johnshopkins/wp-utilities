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
	 * Machine-friendly name of this field group.
	 * Gets assigned to the option key
	 * @var string
	 */
	protected $machinename;

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
	 * ID of this field group
	 * @var sting
	 */
	public $id;

	protected $wordpress;

	public function __construct($type, $machinename, $title, $default, $page, $section, $validation = null)
	{
		$this->type = $type;
		$this->machinename = $machinename;
		$this->title = $title;
		$this->default = $default;
		$this->page = $page;
		$this->section = $section;
		$this->validation = $validation;

		$this->id = "{$this->section}_{$this->machinename}";

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();

		$this->wordpress->add_action("admin_init", array($this, "addField"));
	}


	public function addField()
	{
		$this->wordpress->add_settings_field(
			$this->id,
		    $this->title,
		    array($this, "printField"),
		    $this->page,
		    $this->section,
		    array($this->type, $this->default)  
		);

		$this->wordpress->register_setting($this->page, $this->id, $this->validation);
	}

	public function printField($args)
	{
		$type = $args[0];
		$default = $args[1];

		$method = "get_{$type}";
		echo $this->$method($this->id, $default);
	}

	protected function get_checkbox($optionKey, $default = null)
	{
		$currentValue = $this->wordpress->get_option($optionKey);

		$checked = $this->wordpress->checked(1, $currentValue, false);

		return "<input type=\"checkbox\" id=\"\" name=\"{$name}\" value=\"1\" " . $checked . " />";
	}

	protected function get_text($optionKey, $default = null)
	{
		$currentValue = $this->wordpress->get_option($optionKey);

		$html = "";

		$html .= "<input type=\"text\" id=\"\" name=\"{$optionKey}\" value=\"" . $currentValue . "\" class=\"regular-text\">";

		return $html;
	}

}
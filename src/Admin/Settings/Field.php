<?php

namespace WPUtilities\Admin\Settings;
 
class Field
{
	protected $fieldName;
	protected $fieldDetails;
	protected $fields = array();
	protected $page;
	protected $section;
	protected $validation;
	public $option_id;

	protected $wordpress;

	public function __construct($fieldName, $fieldDetails, $page, $section = "default", $deps = array())
	{
		$this->fieldName = $fieldName;
		$this->fieldDetails = $fieldDetails;

		$this->page = $page;
		$this->section = $section;
		$this->validation = isset($fieldDetails["validation"]) ? $fieldDetails["validation"] : null;

		$this->option_id = $this->section != "default" ? "{$this->section}_{$this->fieldName}" : $this->fieldName;

		$this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new \WPUtilities\WordPressWrapper();

		$this->addField();
	}


	public function addField()
	{
		$this->wordpress->add_action("admin_init", function () {

			$this->wordpress->add_settings_field(
				$this->option_id,
		    $this->fieldDetails["label"],
		    array($this, "printField"),
		    $this->page,
		    $this->section,
		    array($this->fieldDetails)  
			);

			$this->wordpress->register_setting($this->page, $this->option_id, $this->validation);

		});
	}

	public function printField($args)
	{
		$args = array_shift($args);

		$method = "get_{$args['type']}";
		echo $this->$method($args);
	}

	protected function get_checkbox_group($args)
	{
		$args["value"] = $this->wordpress->get_option($this->option_id);
		return FieldCreator::checkbox_group($this->option_id, $args);
	}

	protected function get_text($args)
	{
		$args["value"] = $this->wordpress->get_option($this->option_id);
		return FieldCreator::text($this->option_id, $args);
	}

	protected function get_select($args)
	{
		$args["value"] = $this->wordpress->get_option($this->option_id);
		return FieldCreator::select($this->option_id, $args);
	}

}

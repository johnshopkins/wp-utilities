<?php

namespace WPUtilities\Admin\Settings;

class Section
{
	public $id;
	protected $page;
	protected $machinename;
	protected $title;
	protected $content;

	protected $wordpress;

	public function __construct($page, $machinename, $title, $fields, $content = "")
	{
		$this->page = $page;
		$this->machinename = $machinename;
		$this->title = $title;
		$this->fields = $fields;
		$this->content = $content;

		$this->id = "{$this->page->id}_{$this->machinename}";

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
		
		$this->wordpress->add_action("admin_init", array($this, "addSection"));

		$this->createFields();

	}

	public function addSection()
	{
		$this->wordpress->add_settings_section(
			$this->id,
			$this->title,
			array($this, "addContent"),
			$this->page->id
		);
	}

	public function addContent()
	{
		echo $this->content;
	}

	protected function createFields()
    {
        foreach ($this->fields as $machinename => $details) {

            $validation = isset($details["validation"]) ? $details["validation"] : null;

            // this field has subfields
            if (isset($details["fields"])) {
                new \WPUtilities\Admin\Settings\FieldGroup(
                    $machinename,
                    $details["label"],
                    $details["fields"],
                    $this->page->id,
                    $this->id,
                    $validation
                );
            } else {
                $default = isset($details["default"]) ? $details["default"] : null;
                new \WPUtilities\Admin\Settings\Field(
                    $details["type"],
                    $machinename,
                    $details["label"],
                    $default,
                    $this->page->id,
                    $this->id,
                    $validation
                );
            }
        }
    }
}
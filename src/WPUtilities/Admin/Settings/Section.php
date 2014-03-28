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

	public function __construct($page, $machinename, $title, $content = "")
	{
		$this->page = $page;
		$this->machinename = $machinename;
		$this->title = $title;
		$this->content = $content;

		$this->id = "{$this->page->id}_{$this->machinename}";

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
		
		$this->wordpress->add_action("admin_init", array($this, "addSection"));
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
}
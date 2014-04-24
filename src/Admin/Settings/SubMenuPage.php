<?php

namespace WPUtilities\Admin\Settings;

class SubMenuPage
{
	public $id;

	protected $parent;

	protected $pageTitle;

	protected $menuTitle;

	protected $capability;

	protected $menuSlug;

	protected $content;

	protected $wordpress;

	public function __construct($parent, $pageTitle, $menuTitle, $capability, $menuSlug, $content = "")
	{
		$this->parent = $parent;
		$this->pageTitle = $pageTitle;
		$this->menuTitle = $menuTitle;
		$this->capability = $capability;
		$this->menuSlug = $menuSlug;
		$this->content = $content;

		$this->id = $this->menuSlug;

		$this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();

		$this->wordpress->add_action("admin_menu", array($this, "addPage"));
	}

	public function addPage()
	{
		$this->wordpress->add_submenu_page(
			$this->parent,
			$this->pageTitle,
			$this->menuTitle,
			$this->capability,
			$this->menuSlug,
			array($this, "addContent")
		);
	}

	/**
	 * Handle the display of the admin page
	 * @return null
	 */
	public function addContent() {  
	?>
	    <div class="wrap">

	        <?php $this->wordpress->screen_icon(); ?>
	        <h2><?php echo $this->pageTitle; ?></h2>
	        <?php echo $this->content; ?>

	        <form method="post" action="options.php">

	            <?php $this->wordpress->settings_fields($this->menuSlug); ?>
	            <?php $this->wordpress->do_settings_sections($this->menuSlug); ?>
	            <?php $this->wordpress->submit_button(); ?>

	        </form>

	    </div>
	<?php  
	}
}
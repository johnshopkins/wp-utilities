<?php

namespace WPUtilities\ACF\Fields;

class wysiwyg extends Base
{
  protected $multiple;

  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
  }

  public function getValue($meta)
  {
    $value = parent::getValue($meta);

    $value = $this->wordpress->do_shortcode($value);
    $value = $this->wordpress->wpautop($value);

    return $value;
  }

}

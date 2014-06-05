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
    return $this->wordpress->do_shortcode($value);
  }

}

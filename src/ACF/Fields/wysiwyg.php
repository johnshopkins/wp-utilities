<?php

namespace WPUtilities\ACF\Fields;

class wysiwyg extends Base
{
  protected $multiple;

  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    parent::__construct($fieldData, $id, $parent, $deps);
  }

  public function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return null;

    $value = $this->wordpress->do_shortcode($value);
    $value = $this->wordpress->wpautop($value);

    return $value;
  }

}

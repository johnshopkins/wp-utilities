<?php

namespace WPUtilities\ACF\Fields;

class post_object extends Base
{
  protected $multiple;

  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    return $this->getApiUrl($value);
  }
  
}

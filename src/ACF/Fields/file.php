<?php

namespace WPUtilities\ACF\Fields;

class file extends Base
{
  protected $multiple;

  public function __construct($fieldData, $parent = null)
  {
    parent::__construct($fieldData, $parent);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    return $this->getApiUrl($value);
  }
  
}

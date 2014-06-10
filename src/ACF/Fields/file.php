<?php

namespace WPUtilities\ACF\Fields;

class file extends Base
{
  protected $multiple;

  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return null;

    return $this->getApiUrl($value);
  }
  
}

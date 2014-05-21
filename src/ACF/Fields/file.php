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
    if (empty($value)) return $value;

    $apiUrl = \WPUtilities\API::getApiBase();
    return "{$apiUrl}/{$value}/";
  }
  
}

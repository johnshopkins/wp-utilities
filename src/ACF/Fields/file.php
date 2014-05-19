<?php

namespace WPUtilities\ACF\Fields;

class file extends Base
{
  protected $multiple;

  public function __construct($fieldData, $parent = null)
  {
    parent::__construct($fieldData, $parent);
  }

  public function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return $value;

    $apiUrl = \WPUtilities\API::getApiBase();
    return "{$apiUrl}/{$value}/";
  }

  public function assemble($meta)
  {
    return array(
      $this->fieldName => $this->getValue($meta)
    );
  }
}

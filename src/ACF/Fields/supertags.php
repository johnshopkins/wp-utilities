<?php

namespace WPUtilities\ACF\Fields;

class supertags extends Base
{
  protected $multiple;

  public function __construct($fieldData, $parent = null)
  {
    parent::__construct($fieldData, $parent);
    $this->multiple = $fieldData["multiple"];
  }

  public function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (is_null($value)) $value = array();

    $apiUrl = \WPUtilities\API::getApiBase();
    $value = array_map(function ($id) use ($apiUrl) {
      return "{$apiUrl}/{$id}/";
    }, $value);

    return $this->multiple ? $value : array_shift($value);
  }

  public function assemble($meta)
  {
    return array(
      $this->fieldName => $this->getValue($meta)
    );
  }
}

<?php

namespace WPUtilities\ACF\Fields;

class supertags extends Base
{
  protected $multiple;

  public function __construct($fieldData)
  {
    parent::__construct($fieldData);
    $this->multiple = $fieldData["multiple"];
  }

  public function assemble($meta)
  {
    $currentValue = isset($meta[$this->fieldName]) ? $meta[$this->fieldName] : array();

    $apiUrl = \WPUtilities\API::getApiBase();
    $value = array_map(function ($id) use ($apiUrl) {
      return "{$apiUrl}/{$id}/";
    }, $currentValue);

    return array(
      $this->fieldName => $this->multiple ? $value : array_shift($value)
    );
  }
}

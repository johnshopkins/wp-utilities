<?php

namespace WPUtilities\ACF\Fields;

class relationship extends Base
{
  protected $max;

  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
    $this->max = $fieldData["max"];
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) $value = array();

    $apiUrl = \WPUtilities\API::getApiBase();
    $value = array_map(function ($id) use ($apiUrl) {
      return "{$apiUrl}/{$id}/";
    }, $value);

    return $this->max == 1 ? array_shift($value) : $value;
  }

}

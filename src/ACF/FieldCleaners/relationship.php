<?php

namespace WPUtilities\ACF\FieldCleaners;

class relationship extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) $value = array();

    $apiUrl = \WPUtilities\API::getApiBase();
    $value = array_map(function ($id) use ($apiUrl) {
      return "{$apiUrl}/{$id}/";
    }, $value);

    return $this->field["max"] == 1 ? array_shift($value) : $value;
  }

}

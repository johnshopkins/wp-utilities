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
    $apiUrl = \WordPressAPI\App::$baseUrl;
    $value = array_map(function ($id) use ($apiUrl) {
      return "{$apiUrl}/{$id}/";
    }, $meta[$this->fieldName]);


    $meta = array(
      $this->fieldName => $this->multiple ? $value : array_shift($value)
    );
    
    $this->usedKeys[] = $this->fieldName;

    return $meta;
  }
}

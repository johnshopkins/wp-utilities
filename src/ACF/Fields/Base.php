<?php

namespace WPUtilities\ACF\Fields;

class Base
{
  public $usedKeys = array();

  protected $fieldData = array();

  protected $fieldName;

  public function __construct($fieldData)
  {
    $this->fieldData = $fieldData;
    $this->fieldName = $fieldData["name"];
  }

  public function assemble($meta)
  {
    $currentValue = isset($meta[$this->fieldName]) ? $meta[$this->fieldName] : null;

    return array(
      $this->fieldName => $currentValue
    );
  }
}

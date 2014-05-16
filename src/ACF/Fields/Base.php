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
    $meta = array(
      $this->fieldName => $meta[$this->fieldName]
    );
    
    $this->usedKeys[] = $this->fieldName;

    return $meta;
  }
}

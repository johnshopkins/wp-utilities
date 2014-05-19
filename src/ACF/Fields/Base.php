<?php

namespace WPUtilities\ACF\Fields;

class Base
{
  public $usedKeys = array();

  protected $fieldData = array();

  protected $parent;

  protected $fieldName;

  public function __construct($fieldData, $parent = null)
  {
    $this->fieldData = $fieldData;
    $this->parent = $parent;
    $this->fieldName = $fieldData["name"];
  }

  protected function getValue($meta)
  {
    $value = null;

    if ($this->parent && isset($meta["{$this->parent}_{$this->fieldName}"])) {
      $value = $meta["{$this->parent}_{$this->fieldName}"];
    } else if (isset($meta[$this->fieldName])) {
      $value = $meta[$this->fieldName];
    }

    $this->usedKeys[] = "{$this->parent}_{$this->fieldName}";

    return $value;
  }

  public function assemble($meta)
  {
    return array(
      $this->fieldName => $this->getValue($meta)
    );
  }
}

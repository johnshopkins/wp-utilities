<?php

namespace WPUtilities\ACF\Fields;

class Base
{
  public $usedKeys = array();

  /**
   * WordPress Wrapper
   * @var object
   */
  protected $wordpress;

  protected $fieldData = array();

  protected $parent;

  protected $fieldName;

  public function __construct($fieldData, $parent = null, $deps = array())
  {
    $this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new \WPUtilities\WordPressWrapper();

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

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

  /**
   * Post ID
   * @var integer
   */
  protected $id;

  protected $parent;

  protected $fieldName;

  protected $ansestors;

  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    $this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new \WPUtilities\WordPressWrapper();

    $this->fieldData = $fieldData;
    $this->id = $id;
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

  public function getApiUrl($value)
  {
    if (empty($value)) return $value;

    $apiUrl = \WPUtilities\API::getApiBase();
    return "{$apiUrl}/{$value}/";
  }

  protected function getAnsestors()
  {
    if (is_null($this->ansestors)) {
      $this->ansestors = $this->wordpress->get_post_ancestors($this->id);
    }
    
    return $this->ansestors; 
  }

  protected function findParent($metaKey)
  {
    $ansestors = $this->getAnsestors();

    foreach ($ansestors as $id) {

      $parent = $this->wordpress->get_post_meta($id, $metaKey, true);;
      if ($parent == "inherit") continue;
      return $parent;

    }

    // nothing found
    return null;
  }
}

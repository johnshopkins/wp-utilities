<?php

namespace WPUtilities\ACF\FieldCleaners;

class Base
{
  protected $ansestors = null;

  public function __construct($field, $id)
  {
    $this->wordpress = new \WPUtilities\WordPressWrapper();

    $this->field = $field;
    $this->id = $id;
  }

  protected function getValue()
  {
    return $this->field["value"];
  }

  public function clean()
  {
    return $this->getValue();
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

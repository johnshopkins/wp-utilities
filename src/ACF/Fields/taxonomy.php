<?php

namespace WPUtilities\ACF\Fields;

class taxonomy extends Base
{
  protected $taxonomy;

  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    parent::__construct($fieldData, $id, $parent, $deps);
    $this->taxonomy = $fieldData["taxonomy"];
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) $value = array();

    return array_map(function ($id) {
      $term = $this->wordpress->get_term($id, $this->taxonomy);
      return array(
        "id" => $term->term_id,
        "name" => $term->name,
        "slug" => $term->slug
      );
    }, $value);
  }

}

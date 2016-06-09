<?php

namespace WPUtilities\ACF\FieldCleaners;

class taxonomy extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) $value = array();

    $taxonomy = $this->field["taxonomy"];

    return array_map(function ($id) use ($taxonomy) {
      $term = $this->wordpress->get_term($id, $taxonomy);
      return array(
        "id" => $term->term_id,
        "name" => $term->name,
        "slug" => $term->slug
      );
    }, $value);
  }

}

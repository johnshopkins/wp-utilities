<?php

namespace WPUtilities\Theme\Transformers;

class Page extends Base
{
  public function __construct($deps = array())
  {
    parent::__construct();
  }

  public function parseRegions($post)
  {
    $post->regions = array();
    $regex = "/^region_([^_]+)_(.*)/";

    foreach ($post->meta as $key => &$value) {

      if (!preg_match($regex, $key, $matches)) {
        continue;
      }

      $region = $matches[1];
      $field = $matches[2];

      $post->regions[$region][$field] = $value;
      unset($post->meta->$key);

    }

    return $post;
  }

}

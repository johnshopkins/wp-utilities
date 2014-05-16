<?php

namespace WPUtilities\Theme\Transformers;

class Page extends Base
{
  public function __construct($deps = array())
  {
    parent::__construct();

    $supertags = $this->contentTypes->findSupertags();
    $this->supertags = $supertags["page"];
  }

  public function parseRegions($post)
  {
    $post->regions = array();
    $regex = "/^region_([^_]+)_(.*)/";

    foreach ($post->meta as $key => &$value) {

      if (!preg_match($regex, $key, $matches)) {
        continue;
      }

      $value = $this->compileSupertags($key, $value);

      $region = $matches[1];
      $field = $matches[2];

      $post->regions[$region][$field] = $value;
      unset($post->meta->$key);

    }

    return $post;
  }

  protected function compileSupertags($key, $value)
  {
    if (!$value) return $value;
    if (!isset($this->supertags[$key])) return $value;

    $supertag = $this->supertags[$key];

    if (isset($supertag["children"])) {

      // this is a repeater field with supertags as children

      $children = $supertag["children"];
      $childFieldNames = array_keys($children);

      if (count($children) == 1) {

        // this repeater only has one child. When we first got the
        // meta, we pushed the values out of the subfield array into
        // the main array. So now we need to loop through the main value

        foreach ($value as &$v) {
          $v = $this->getDataFromApi($v);
        }
          
      } else {

        foreach ($value as &$row) {

          foreach ($row as $subFieldName => &$v) {
            // not a supertag field
            if (!in_array($subFieldName, $childFieldNames)) continue;
            $v = $this->getDataFromApi($v);
          }

        }

      }

    } else {

      // top-level supertag
      
      $value = $this->getDataFromApi($value);
    }

    return $value;
  }

  protected function getDataFromApi($url)
  {
    return $this->api->get($url)->data;
  }

}

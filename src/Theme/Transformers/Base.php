<?php

namespace WPUtilities\Theme\Transformers;

class Base
{
  /**
   * WPUtilities\API
   * @var string
   */
  protected $api;

  protected $contentTypes;

  /**
   * Field types whose data needs
   * to be fetched from the API
   * @var array
   */
  protected $fetch = array(
    "supertags",
    "file"
  );

  public function __construct($deps = array())
  {
    $this->api = isset($deps["api"]) ? $deps["api"] : new \WPUtilities\API();
    $contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : new \WPUtilities\ACF\ContentTypes();
    $this->contentTypes = $contentTypes->find();
  }

  public function getPost($id)
  {
    $data = $this->api->get("/{$id}")->data;
    return $this->parseFields($data);
  }

  /**
   * Loop through all the fields and modify
   * the value, if necessary.
   *
   * Currently transforming API URLs into
   * the data at that endoiint
   * @param  object $post Post
   * @return obejct Modified post
   */
  protected function parseFields($post)
  {
    $fields = $this->contentTypes["page"];

    foreach ($post->meta as $key => $value) {

      $field = $fields[$key];

      // no value or (not a repeater and not a fetch field)
      if (empty($value) || ($field["type"] != "repeater" && !in_array($field["type"], $this->fetch))) continue;

      if ($field["type"] == "repeater" && count($field["sub_fields"]) > 1) {
        
        $children = array();

        foreach ($field["sub_fields"] as $kid) {
          $children[$kid['name']] = $kid;
        }

        foreach ($value as $row) {

          foreach ($row as $rowField => &$fieldValue) {
              if (!in_array($children[$rowField]["type"], $this->fetch)) continue;
              $fieldValue = $this->fetch($fieldValue);
              continue;
          }

          
        }

      } else {
        $post->meta->$key = $this->fetch($value);
      }      

    }

    return $post;
  }

  protected function fetch($value)
  {
    if (is_array($value)) {
      $value = array_map(function ($url) {
        return $this->api->get($url)->data;
      }, $value);
    } else {
      $value = $this->api->get($value)->data;
    }

    return $value;
  }

}

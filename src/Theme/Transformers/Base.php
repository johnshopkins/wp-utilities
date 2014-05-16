<?php

namespace WPUtilities\Theme\Transformers;

class Base
{
  /**
   * WPUtilities\API
   * @var string
   */
  protected $api;

  public function __construct($deps = array())
  {
    $this->api = isset($deps["api"]) ? $deps["api"] : new \WPUtilities\API();
    $this->contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : new \WPUtilities\ACF\ContentTypes();
  }

  public function getPost($id)
  {
    return $this->api->get("/{$id}")->data;
  }

}

<?php

namespace WPUtilities\Theme\Transformers;

class Base
{
  /**
   * WPUtilities\API
   * @var string
   */
  protected $api;

  /**
   * WPUtilities\Post
   * @var object
   */
  protected $postUtil;

  public function __construct($deps = array())
  {
    $this->api = isset($deps["api"]) ? $deps["api"] : new \WPUtilities\API();
    $this->postUtil = isset($deps["postUtil"]) ? $deps["postUtil"] : new \WPUtilities\Post();
  }

  /**
   * Add nicely formatted metadata to a given post.
   * @param object $post Post object
   * @return object Post obejct
   */
  public function addMeta($post)
  {
    $post->meta = $this->postUtil->getMeta($post->ID, $post->post_type);
    return $post;
  }

}

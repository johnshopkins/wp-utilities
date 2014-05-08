<?php

namespace WPUtilities;

class API
{
  
  protected $http;
  protected $apiBase;

  public function __construct($deps = array())
  {
    $this->http = isset($deps["http"]) ? $deps["http"] : new \HttpExchange\Adapters\Resty(new \Resty());
    $this->apiBase = isset($deps["apiBase"]) ? $deps["apiBase"] : \WPUtilities\API::getApiBase();
  }

  public static function getApiBase($env = null)
  {
    $env = is_null($env) ? ENV : $env;

    $prefix = "";

    if ($env != "production") {
      $prefix = $env . ".";
    }

    return "http://{$prefix}jhu.edu/api";
  }

  public function get($endpoint)
  {
    return $this->http->get($this->apiBase . $endpoint)->getBody();
  }

}

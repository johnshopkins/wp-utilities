<?php

namespace WPUtilities;

class API
{
  public $apiBase;
  protected $http;

  public function __construct($deps = array(), $admin = false)
  {
    $this->http = isset($deps["http"]) ? $deps["http"] : new \HttpExchange\Adapters\Resty(new \Resty\Resty());
    $this->apiBase = isset($deps["apiBase"]) ? $deps["apiBase"] : \WPUtilities\API::getApiBase(null, $admin);
  }

  public static function getApiBase($env = null, $admin = false)
  {
    $env = is_null($env) ? ENV : $env;

    $prefix = "www";

    if ($env == "production") {
      $prefix = $admin ? "origin-beta1" : "beta";
    } else {
      // $prefix = $admin ? "{$env}-test" : $env;
      $prefix = $env;
    }

    return "https://{$prefix}.jhu.edu/api";
  }

  public function get($endpoint, $params = array(), $headers = array(), $options = array())
  {
    if (substr($endpoint, 0, strlen($this->apiBase)) != $this->apiBase) {

      if (substr($endpoint, 0, 1) != "/") {
        $endpoint = "/{$endpoint}";
      }

      $endpoint = $this->apiBase . $endpoint;
    }

    return $this->http->get($endpoint, $params, $headers, $options)->getBody();
  }

}

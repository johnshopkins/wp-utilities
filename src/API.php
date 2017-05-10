<?php

namespace WPUtilities;

class API
{
  public $apiBase;
  protected $http;

  public function __construct($logger, $admin = false)
  {
    $this->logger = $logger;
    $this->http = new \HttpExchange\Adapters\Guzzle(new \GuzzleHttp\Client(array("debug" => true)), $this->logger);
    $this->apiBase = \WPUtilities\API::getApiBase(null, $admin);
    $this->admin = $admin;
  }

  public static function getApiBase($env = null, $admin = false)
  {
    $env = is_null($env) ? ENV : $env;

    $prefix = "www";

    if ($env == "production") {
      $prefix = $admin ? "origin-beta1" : "www";
    } else {
      $prefix = $env;
    }

    return "https://{$prefix}.jhu.edu/api";
  }

  /**
   * If this is an absolute URL, get rid of everything up to /api and
   * recraft the base URL. This is necessary when the requested endpoint
   * is https://beta, but the request needs to be made to https://origin-beta
   * because the API has been set to admin mode.
   */
  public function removeBaseUrl($endpoint)
  {
    return preg_replace("/^(.*?)\.jhu\.edu\/api/", "", $endpoint);
  }

  public function get($endpoint, $params = array(), $headers = array(), $options = array())
  {
    $endpoint = $this->removeBaseUrl($endpoint);

    if (substr($endpoint, 0, 1) != "/") {
      $endpoint = "/{$endpoint}";
    }

    $endpoint = $this->apiBase . $endpoint;

    if ($this->admin) {
      $options["verify"] = false;
    }

    $response = $this->http->get($endpoint, $params, $headers, $options);

    $body = $response->getBody();
    $status = $response->getStatusCode();

    if ($status !== 200) {
      $body = array(
        "error" => array(
          "code" => $status
        )
      );

      // force object
      $body = json_decode(json_encode($body));
    }

    return $body;
  }

}

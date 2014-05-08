<?php
namespace WPUtilities;

class APITest extends BaseTest
{
  public function setUp()
  {
    $this->testClass = new API(array(
        "http" => $this->getHttp()));
    parent::setup();
  }

  public function testGetApiBase()
  {
    $method = $this->getMethod("getApiBase");

    $result = $method->invoke($this->testClass, "production");
    $this->assertEquals("http://jhu.edu/api", $result);

    $result = $method->invoke($this->testClass, "staging");
    $this->assertEquals("http://staging.jhu.edu/api", $result);

    $result = $method->invoke($this->testClass, "local");
    $this->assertEquals("http://local.jhu.edu/api", $result);
  }

  public function testGet()
  {
    $result = $this->testClass->get("endpoint");
    $this->assertEquals("the body", $result);
  }

  protected function getHttp()
  {
    $http = $this->getMockBuilder("\\HttpExchange\\Adapters\\Resty")
      ->disableOriginalConstructor()
      ->getMock();

    $http->expects($this->any())
      ->method("get")
      ->will($this->returnSelf());

    $http->expects($this->any())
      ->method("getBody")
      ->will($this->returnValue("the body"));

    return $http;
  }
}

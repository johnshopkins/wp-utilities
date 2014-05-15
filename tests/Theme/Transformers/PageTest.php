<?php

namespace WPUtilities\Theme\Transformers;

class PageTest extends \WPUtilities\BaseTest
{

  public function setUp()
  {
    $this->testClass = new Page(array(
      "api" => $this->getApi(),
      "postUtil" => $this->getPostUtil(),
      "contentTypes" => $this->getContentTypes()
    ));
    parent::setup();
  }

  public function testCompileRegion()
  {
    $given = array(
      array(10),
      array(20)
    );

    $expected = array(
      "post 10",
      "post 20"
    );

    $result = $this->testClass->compileRegion($given);
    $this->assertEquals($expected, $result);

  }

  protected function getApi()
  {
    $api = $this->getMockBuilder("\\WPUtilities\\API")
      ->disableOriginalConstructor()
      ->getMock();

    $object10 = new \StdClass();
    $object10->data = "post 10";

    $object20 = new \StdClass();
    $object20->data = "post 20";

    $api->expects($this->any())
      ->method("get")
      ->will($this->returnValueMap(array(
        array("/10", array(), $object10),
        array("/20", array(), $object20),
      )));

    return $api;
  }

  protected function getPostUtil()
  {
    $postUtil = $this->getMockBuilder("\\WPUtilities\\Post")
      ->disableOriginalConstructor()
      ->getMock();

    return $postUtil;
  }

  protected function getContentTypes()
  {   
    $contentTypes = $this->getMockBuilder("\\WPUtilities\\ACF\\ContentTypes")
      ->disableOriginalConstructor()
      ->getMock();

    return $contentTypes;
  }

}

<?php

namespace WPUtilities\Theme\Transformers;

class BaseTest extends \WPUtilities\BaseTest
{

  public function setUp()
  {
    $this->testClass = new Base(array(
      "api" => $this->getApi(),
      "postUtil" => $this->getPostUtil(),
      "contentTypes" => $this->getContentTypes()
    ));
    parent::setup();
  }

  public function testAddMeta()
  {
    $given = new \StdClass();
    $given->ID = 10;
    $given->post_type = "post";

    $expected = new \StdClass();
    $expected->ID = 10;
    $expected->post_type = "post";
    $expected->meta = "meta";


    $result = $this->testClass->addMeta($given);
    $this->assertEquals($expected, $result);
  }

  protected function getApi()
  {
    $api = $this->getMockBuilder("\\WPUtilities\\API")
      ->disableOriginalConstructor()
      ->getMock();

    return $api;
  }

  protected function getPostUtil()
  {
    $postUtil = $this->getMockBuilder("\\WPUtilities\\Post")
      ->disableOriginalConstructor()
      ->getMock();

    $postUtil->expects($this->any())
      ->method("getMeta")
      ->will($this->returnValue("meta"));

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

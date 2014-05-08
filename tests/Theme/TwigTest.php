<?php

namespace WPUtilities\Theme;

class TwigTest extends \WPUtilities\BaseTest
{

  public function setUp()
  {
    $this->testClass = new Twig("someDir", array(
      "twig" => $this->getTwig()
    ));
    parent::setup();
  }

  public function testDisplay()
  {
    $result = $this->testClass->display("template.html", array("data" => "data"));
    $this->assertEquals($result, "template displayed");
  }

  protected function getTwig()
  {
    $twig = $this->getMockBuilder("\\Twig_Environment")
      ->disableOriginalConstructor()
      ->getMock();

    $twig->expects($this->any())
      ->method("display")
      ->will($this->returnValue("template displayed"));

    return $twig;
  }

}

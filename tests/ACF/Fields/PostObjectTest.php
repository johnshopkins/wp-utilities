<?php
namespace WPUtilities\ACF\Fields;

class PostObjectTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "post_object",
      "name" => "post_field"
    );

    $this->testClass = new post_object($fieldData, 100);

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("post_field" => ""));
    $this->assertEquals(array("post_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("post_field" => 1234));
    $this->assertEquals(array("post_field" => "http://local.jhu.edu/api/1234/"), $result);
  }
  
}

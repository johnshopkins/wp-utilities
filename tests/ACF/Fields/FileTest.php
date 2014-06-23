<?php
namespace WPUtilities\ACF\Fields;

class FileTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "file",
      "name" => "file_field"
    );

    $this->testClass = new file($fieldData, 100);

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("file_field" => ""));
    $this->assertEquals(array("file_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("file_field" => 1234));
    $this->assertEquals(array("file_field" => "http://local.jhu.edu/api/1234/"), $result);
  }
  
}

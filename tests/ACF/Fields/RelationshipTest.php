<?php
namespace WPUtilities\ACF\Fields;

class RelationshipTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "relationship",
      "name" => "relationship_field",
      "max" => 1
    );

    $this->testClass = new relationship($fieldData, 100);

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("relationship_field" => ""));
    $this->assertEquals(array("relationship_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("relationship_field" => array(1234)));
    $this->assertEquals(array("relationship_field" => "http://local.jhu.edu/api/1234/"), $result);

    // change max
    $this->setProperty("max", null);

    $result = $method->invoke($this->testClass, array("relationship_field" => array(2345, 3456)));
    $this->assertEquals(array("relationship_field" => array(
      "http://local.jhu.edu/api/2345/",
      "http://local.jhu.edu/api/3456/"
    )), $result);
  }
  
}

<?php
namespace WPUtilities\ACF\Fields;

class RelatedContentPickerTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "related_content_picker",
      "name" => "related_field"
    );

    $this->testClass = new related_content_picker($fieldData, 100, null, array(
      "wordpress" => $this->getWordPress()
    ));

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("related_field" => ""));
    $this->assertEquals(array("related_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("related_field" => "7654"));
    $this->assertEquals(array("related_field" => "http://local.jhu.edu/api/7654/"), $result);

    $result = $method->invoke($this->testClass, array("related_field" => "inherit"));
    $this->assertEquals(array("related_field" => "http://local.jhu.edu/api/8989/"), $result);
  }

  protected function getWordPress()
  {   
    $wordpress = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
      ->disableOriginalConstructor()
      ->getMock();

    $wordpress->expects($this->any())
      ->method("__call")
      ->will($this->returnValueMap(array(
        array("get_post_ancestors", array(100), array(101, 102)),
        array("get_post_meta", array(101, "region_related_sidebar", true), "inherit"),
        array("get_post_meta", array(102, "region_related_sidebar", true), "8989")
      )));

    return $wordpress;
  }
  
}

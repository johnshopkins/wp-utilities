<?php
namespace WPUtilities\ACF\Fields;

class MenuPickerTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "menu_picker",
      "name" => "menu_field"
    );

    $this->testClass = new menu_picker($fieldData, 100, null, array(
      "wordpress" => $this->getWordPress()
    ));

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("menu_field" => ""));
    $this->assertEquals(array("menu_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("menu_field" => "a_menu"));
    $this->assertEquals(array("menu_field" => "a_menu"), $result);

    $result = $method->invoke($this->testClass, array("menu_field" => "inherit"));
    $this->assertEquals(array("menu_field" => "another_menu"), $result);


    // change ID to find new parents
    $this->setProperty("id", 200);
    $this->setProperty("ansestors", null);

    $result = $method->invoke($this->testClass, array("menu_field" => "inherit"));
    $this->assertEquals(array("menu_field" => null), $result);
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
        array("get_post_meta", array(101, "menu", true), "inherit"),
        array("get_post_meta", array(102, "menu", true), "another_menu"),

        array("get_post_ancestors", array(200), array(201)),
        array("get_post_meta", array(201, "menu", true), "inherit")
      )));

    return $wordpress;
  }

}

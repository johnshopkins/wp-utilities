<?php
namespace WPUtilities\ACF\Fields;

class JhuColorPickerTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "jhu_color_picker",
      "name" => "color_field"
    );

    $this->testClass = new jhu_color_picker($fieldData, 100);

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("color_field" => ""));
    $this->assertEquals(array("color_field" => null), $result);

    $color = new \StdClass();
    $color->hex = "fff";

    $result = $method->invoke($this->testClass, array("color_field" => $color));
    $this->assertEquals(array("color_field" => $color), $result);
  }
  
}

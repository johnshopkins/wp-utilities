<?php
namespace WPUtilities\ACF\Fields;

class WysiwygTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "wysiwyg",
      "name" => "wysiwyg_field"
    );

    $this->testClass = new wysiwyg($fieldData, 100, null, array(
      "wordpress" => $this->getWordPress()
    ));

    parent::setup();
  }

  public function testAssemble()
  {
    $method = $this->getMethod("assemble");

    $result = $method->invoke($this->testClass, array("wysiwyg_field" => ""));
    $this->assertEquals(array("wysiwyg_field" => null), $result);
    
    $result = $method->invoke($this->testClass, array("wysiwyg_field" => "some text"));
    $this->assertEquals(array("wysiwyg_field" => "some text transformed again"), $result);
  }

  public function testAbsoluteToRelativeLinks() 
  {
    $method = $this->getMethod("absoluteToRelativeLinks");

    $given = "http://local.jhu.edu";
    $expected = "/";

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);

    $given = "http://staging.jhu.edu/";
    $expected = "/";

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);

    $given = "http://somethingelse.jhu.edu/testing/testing";
    $expected = "/testing/testing";

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);

    $given = "http://jhu.edu/testing/testing http://local.jhu.edu/testing/testing";
    $expected = "/testing/testing /testing/testing";

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);

  }
  
  protected function getWordPress()
  {   
    $wordpress = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
      ->disableOriginalConstructor()
      ->getMock();

    $wordpress->expects($this->any())
      ->method("__call")
      ->will($this->returnValueMap(array(
        array("do_shortcode", array("some text"), "some text transformed"),
        array("wpautop", array("some text transformed"), "some text transformed again"),
        array("get_home_url", array(), "http://jhu.edu")
      )));

    return $wordpress;
  }

}

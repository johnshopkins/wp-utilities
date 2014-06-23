<?php
namespace WPUtilities\ACF\Fields;

class RepeaterTest extends \WPUtilities\BaseTest
{
  public function setUp()
  {
    $fieldData = array(
      "type" => "repeater",
      "name" => "hobbies",
      "row_limit" => "",
      "sub_fields" => array(
        array(
          "type" => "text",
          "name" => "hobby"
        )
      )
    );

    $this->testClass = new repeater($fieldData, 100);

    parent::setup();
  }

  public function testAssemble_OneSubField()
  {
    $method = $this->getMethod("assemble");

    $given = array(
      "hobbies_0_hobby" => "Baseball",
      "hobbies_1_hobby" => "Football"
    );

    $expected = array("hobbies" => array("Baseball", "Football"));

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);
  }

  public function testAssemble_OneFieldAsRelationship()
  {
    // setup new field
    $this->setupNewField(array(
      "type" => "repeater",
      "name" => "people",
      "row_limit" => "",
      "sub_fields" => array(
        array(
          "type" => "relationship",
          "name" => "person",
          "post_type" => array("person"),
          "max" => 1
        )
      )
    ));

    // and test
    $method = $this->getMethod("assemble");

    $given = array(
      "people_0_person" => array(1234)
    );

    $expected = array("people" => array("http://local.jhu.edu/api/1234/"));

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);
  }

  public function testAssemble_MultipleSubfieldsOneAsRelationship()
  {
    // setup new field
    $this->setupNewField(array(
      "type" => "repeater",
      "name" => "location_ratings",
      "sub_fields" => array(
        array(
          "type" => "relationship",
          "name" => "location",
          "post_type" => array("location"),
          "max" => 1
        ),
        array(
          "type" => "text",
          "name" => "rating"
        )
      ),
      "row_limit" => ""
    ));

    // and test
    $method = $this->getMethod("assemble");

    $given = array(
      "location_ratings_0_location" => array(1234),
      "location_ratings_0_rating" => "5 stars",
      "location_ratings_1_location" => array(5678),
      "location_ratings_1_rating" => "4 stars",
    );

    $expected = array("location_ratings" => array(
      array(
        "location" => "http://local.jhu.edu/api/1234/",
        "rating" => "5 stars"
      ),
      array(
        "location" => "http://local.jhu.edu/api/5678/",
        "rating" => "4 stars"
      )
    ));

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);
  }

  public function testAssemble_multipleSubfields()
  {
    // setup new field
    $this->setupNewField(array(
      "type" => "repeater",
      "name" => "location_ratings",
      "sub_fields" => array(
        array(
          "type" => "relationship",
          "name" => "location",
          "post_type" => array("location"),
          "max" => 1
        ),
        array(
          "type" => "text",
          "name" => "rating"
        )
      ),
      "row_limit" => ""
    ));

    // and test
    $method = $this->getMethod("assemble");

    $given = array(
      "location_ratings_0_location" => array(1234),
      "location_ratings_0_rating" => "5 stars",
      "location_ratings_1_location" => array(5678),
      "location_ratings_1_rating" => "4 stars",
    );

    $expected = array("location_ratings" => array(
      array(
        "location" => "http://local.jhu.edu/api/1234/",
        "rating" => "5 stars"
      ),
      array(
        "location" => "http://local.jhu.edu/api/5678/",
        "rating" => "4 stars"
      )
    ));

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);
  }

  public function testAssemble_multipleSubfieldsLimitOneRow()
  {
    // setup new field
    $this->setupNewField(array(
      "type" => "repeater",
      "name" => "car",
      "row_limit" => 1,
      "sub_fields" => array(
        array(
          "type" => "text",
          "name" => "make"
        ),
        array(
          "type" => "text",
          "name" => "model"
        )
      )
    ));

    // and test
    $method = $this->getMethod("assemble");

    $given = array(
      "car_0_make" => "honda",
      "car_0_model" => "civic"
    );

    $expected = array("car" => array(
      "make" => "honda",
      "model" => "civic"
    ));

    $result = $method->invoke($this->testClass, $given);
    $this->assertEquals($expected, $result);
  }

  protected function setupNewField($fieldData)
  {
    $this->setProperty("fieldName", $fieldData["name"]);
    $this->setProperty("subfields", $fieldData["sub_fields"]);
    $this->setProperty("fieldData", $fieldData);
  }

}

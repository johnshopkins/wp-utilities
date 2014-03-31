<?php
namespace tests\WPUtilities\ACF;

class RepeaterTest extends \tests\Base
{
    public function setUp()
    {
        $this->testClass = new \WPUtilities\ACF\Repeater();
        parent::setup();
    }

    public function testCleanMetaNoRepeaters()
    {
        $given = $expected = array(
            "key" => "value"
        );

        $result = $this->testClass->cleanMeta($given);
        $this->assertEquals($expected, $result);
    }

    public function testCleanMetaRepeaters()
    {
        $given = array(
            "name" => 2,
            "name_0_firstname" => "John",
            "name_0_lastname" => "Smith",
            "name_0_zipcode" => 21212,
            "name_1_firstname" => "Jane",
            "name_1_lastname" => "Doe",
            "name_1_zipcode" => 21210
        );

        $expected = array(
            "name" => array(
                array(
                    "firstname" => "John",
                    "lastname" => "Smith",
                    "zipcode" => 21212
                ),
                array(
                    "firstname" => "Jane",
                    "lastname" => "Doe",
                    "zipcode" => 21210
                )
            )
        );
        
        $result = $this->testClass->cleanMeta($given);
        $this->assertEquals($expected, $result);
    }

    public function testCleanMetaRepeatersWithOneSubfield()
    {
        $given = array(
            "name" => 2,
            "name_0_firstname" => "John",
            "name_1_firstname" => "Jane",
        );

        $expected = array(
            "name" => array(
                "John",
                "Jane"
            )
        );
        
        $result = $this->testClass->cleanMeta($given);
        $this->assertEquals($expected, $result);
    }

    public function testCleanMetaRepeatersWithSubfieldHyphens()
    {
        $given = array(
            "name" => 2,
            "name_0_first-name" => "John",
            "name_0_last-name" => "Smith",
            "name_0_zip-code" => 21212,
            "name_1_first-name" => "Jane",
            "name_1_last-name" => "Doe",
            "name_1_zip-code" => 21210
        );

        $expected = array(
            "name" => array(
                array(
                    "first-name" => "John",
                    "last-name" => "Smith",
                    "zip-code" => 21212
                ),
                array(
                    "first-name" => "Jane",
                    "last-name" => "Doe",
                    "zip-code" => 21210
                )
            )
        );
        
        $result = $this->testClass->cleanMeta($given);
        $this->assertEquals($expected, $result);
    }

    public function testSquashSimpleRepeaterNeedsSquashing()
    {
        $given = array(
            array("id" => 123),
            array("id" => 456)
        );

        $expected = array(123, 456);

        $method = $this->getMethod("squashSimpleRepeater");
        $result = $method->invoke($this->testClass, $given);
        
        $this->assertEquals($expected, $result);
    }

    public function testSquashSimpleRepeaterDoesNotNeedSquashing()
    {
        $method = $this->getMethod("squashSimpleRepeater");

        $given = $expected = "String value";
        $result = $method->invoke($this->testClass, $given);
        $this->assertEquals($expected, $result);

        $given = $expected = array(
            array(
                "one" => "two",
                "three" => "four"
            ),
            array(
                "one" => "two",
                "three" => "four"
            )
        );

        $result = $method->invoke($this->testClass, $given);
        $this->assertEquals($expected, $result);
    }

    public function testCreateRepeater()
    {
        $given = array(
            "images" => array(
                array(
                    "type" => "low_resolution",
                    "url" => "a url"
                ),
                array(
                    "type" => "thumbnail",
                    "url" => "another url"
                )
            )
        );

        $expected = array(
            "images" => 2,
            "images_0_type" => "low_resolution",
            "images_0_url" => "a url",
            "images_1_type" => "thumbnail",
            "images_1_url" => "another url"
        );

        $result = $this->testClass->createRepeater($given);
        $this->assertEquals($expected, $result);
    }
}
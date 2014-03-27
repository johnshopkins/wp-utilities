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
            "name_1_firstname" => "Jane",
            "name_1_lastname" => "Doe"
        );

        $expected = array(
            "name" => array(
                array(
                    "firstname" => "John",
                    "lastname" => "Smith"
                ),
                array(
                    "firstname" => "Jane",
                    "lastname" => "Doe"
                )
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
            "name_1_first-name" => "Jane",
            "name_1_last-name" => "Doe"
        );

        $expected = array(
            "name" => array(
                array(
                    "first-name" => "John",
                    "last-name" => "Smith"
                ),
                array(
                    "first-name" => "Jane",
                    "last-name" => "Doe"
                )
            )
        );
        
        $result = $this->testClass->cleanMeta($given);
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
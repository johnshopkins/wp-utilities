<?php
namespace WPUtilities\ACF;

class RepeaterTest extends \WPUtilities\BaseTest
{
    public function setUp()
    {
        $this->testClass = new Repeater();
        parent::setup();
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

<?php
namespace tests\ACFUtilities;

class RepeaterTest extends \tests\Base
{
    public function setUp()
    {
        $this->testClass = new \ACFUtilities\Repeater();
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
}
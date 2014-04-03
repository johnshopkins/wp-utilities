<?php
namespace tests\WPUtilities\ACF;

class RepeaterTest extends \tests\Base
{
    public function setUp()
    {
        $this->testClass = new \WPUtilities\ACF\Repeater(array(
            "wordpress" => $this->getWordPress(),
            "wordpress_query" => $this->getWordPressQuery()
        ));
        parent::setup();
    }

    // public function testCleanMetaNoRepeaters()
    // {
    //     $given = $expected = array(
    //         "key" => "value"
    //     );

    //     $result = $this->testClass->cleanMeta($given);
    //     $this->assertEquals($expected, $result);
    // }

    public function testCleanMetaRepeaters()
    {
        $given = array(
            "name" => 2,
            "name_0_firstname" => "John",
            "name_0_lastname" => "Smith",
            "name_0_zipcode" => 21212,
            "name_1_firstname" => "Jane",
            "name_1_lastname" => "Doe",
            "name_1_zipcode" => 21210,

            // one subfield
            "ids" => 2,
            "ids_0_id" => 12345,
            "ids_1_id" => 67890,

            // repeater with no fields
            "some_field" => 0,
            "_some_field" => "field_123456",

            // some field with 0 value
            "another_field" => 0,

            // hidden field that doesn't follow ACF
            "yet_another" => 0,
            "_yet_another" => "tricky"
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
            ),
            "ids" => array(12345, 67890),
            "some_field" => 0,
            "_some_field" => "field_123456",
            "another_field" => 0,
            "yet_another" => 0,
            "_yet_another" => "tricky"
        );
        
        // first test (some_field IS NOT a repeater)
        $result = $this->testClass->cleanMeta($given);
        $this->assertEquals($expected, $result);


        // second test (some_field IS a repeater)
        $expected["some_field"] = array();
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

    protected function getWordPress()
    {   
        $wordpress_query = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $wordpress_query->expects($this->any())
            ->method("__call")
            ->with("get_post_meta", $this->anything())
            ->will($this->onConsecutiveCalls(array("type" => "text"), array("type" => "repeater")));

        return $wordpress_query;
    }

    protected function getWordPressQuery()
    {   
        $wordpress_query = $this->getMockBuilder("\\WPUtilities\\WPQueryWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $result = new \StdClass();
        $result->post = new \StdClass();
        $result->post->ID = 10;

        $wordpress_query->expects($this->any())
            ->method("run")
            ->will($this->returnValue($result));

        return $wordpress_query;
    }
}
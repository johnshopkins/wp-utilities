<?php
namespace WPUtilities\ACF;

class RepeaterTest extends \WPUtilities\BaseTest
{
    public function setUp()
    {
        $contentTypes = array(
            "no_repeater" => array(
                array(
                    "type" => "text",
                    "name" => "content"
                ),
            ),
            "post" => array(
                array(
                    "type" => "supertags",
                    "name" => "connections",
                    "vocabs" => array("profile", "person", "location"),
                    "multiple" => 1
                ),
                array(
                    "type" => "text",
                    "name" => "content"
                ),
                array(
                    "type" => "repeater",
                    "name" => "authors",
                    "sub_fields" => array(
                        array(
                            "name" => "firstname",
                            "type" => "text"
                        ),
                        array(
                            "name" => "lastname",
                            "type" => "text"
                        )
                    )
                ),
                array(
                    "type" => "repeater",
                    "name" => "media",
                    "sub_fields" => array(
                        array(
                            "name" => "file",
                            "type" => "file"
                        )
                    )
                ),
                array(
                    "type" => "repeater",
                    "name" => "addresses",
                    "sub_fields" => array(
                        array(
                            "name" => "address",
                            "type" => "text"
                        )
                    )
                ),
                array(
                    "type" => "supertags",
                    "name" => "profile",
                    "vocabs" => array("profile"),
                    "multiple" => 0
                )
            )
        );

        $this->testClass = new Repeater(array(
            "contentTypes" => $contentTypes,
            "wordpress" => $this->getWordPress(),
            "wordpress_query" => $this->getWordPressQuery()
        ));
        parent::setup();
    }

    public function testCleanMetaNoRepeaters()
    {
        $given = array(
            "text" => "value"
        );

        $expected = array(
            "text" => "value"
        );

        $result = $this->testClass->cleanMeta($given, "no_repeater");
        $this->assertEquals($expected, $result);
    }

    public function testCleanMetaRepeaters()
    {
        $given = array(
            "authors" => 2,
            "authors_0_firstname" => "John",
            "authors_0_lastname" => "Smith",
            "authors_1_firstname" => "Jane",
            "authors_1_lastname" => "Doe",
            "authors_1_zipcode" => 21236, // field posing as part of the repeater

            // one subfield
            "media" => 2,
            "media_0_file" => 12345,
            "media_1_file" => 67890,

            // hidden field that doesn't follow ACF
            "yet_another" => 0,
            "_yet_another" => "tricky"
        );

        $expected = array(
            "authors" => array(
                array(
                    "firstname" => "John",
                    "lastname" => "Smith"
                ),
                array(
                    "firstname" => "Jane",
                    "lastname" => "Doe"
                )
            ),
            "authors_1_zipcode" => 21236,
            "media" => array(12345, 67890),
            "addresses" => array(),
            "yet_another" => 0,
            "_yet_another" => "tricky"
        );
        
        // first test (some_field IS NOT a repeater)
        $result = $this->testClass->cleanMeta($given, "post");
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
        $wordpress = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $wordpress->expects($this->any())
            ->method("__call")
            ->with("get_post_meta", $this->anything())
            ->will($this->onConsecutiveCalls(array("type" => "text"), array("type" => "repeater")));

        return $wordpress;
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
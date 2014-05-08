<?php
namespace WPUtilities\ACF;

class SupertagsTest extends \WPUtilities\BaseTest
{
    protected $fieldGroups;

    protected $rules = array();
    protected $wordpress_meta = array();
    protected $fieldGroupPosts = array();
    protected $cleanedFieldGroups = array();

    public function setUp()
    {
        $contentTypes = array(
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

        $this->testClass = new Supertags($contentTypes);

        parent::setup();
    }

    public function testFind()
    {
        $result = $this->testClass->find();

        $expected = array(
            "post" => array(
                "profile" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1
                    ),
                    array(
                        "name" => "profile",
                        "multiple" => 0
                    )
                ),
                "person" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1
                    )
                ),
                "location" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1
                    )
                )
            )
        );

        $this->assertEquals($expected, $result);
    }

    protected function getContentTypes()
    {   
        $contentTypes = $this->getMockBuilder("\\WPUtilities\\ACF\\ContentTypes")
            ->disableOriginalConstructor()
            ->getMock();

        $fields = array(
            array(
                 "type" => "notSupertags"
            ),
            array(
                "type" => "supertags",
                "name" => "campus_stuff",
                "vocabs" => array("location", "division"),
                "multiple" => 1
            ),
            array(
                "type" => "supertags",
                "name" => "campus_location",
                "vocabs" => array("location"),
                "multiple" => 0
            )
        );

        $contentTypes->expects($this->any())
            ->method("find")
            ->will($this->returnValue(array(
                "person" => $fields
            )));

        return $contentTypes;
    }
}

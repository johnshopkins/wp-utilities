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

                // fake out
                array(
                    "type" => "text",
                    "name" => "test"
                ),

                // anything
                array(
                    "type" => "supertags",
                    "name" => "connections",
                    "vocabs" => array("profile", "person", "location"),
                    "multiple" => 1
                ),

                // just one
                array(
                    "type" => "supertags",
                    "name" => "profile",
                    "vocabs" => array("profile"),
                    "multiple" => 0
                ),

                // some more content
                array(
                    "type" => "repeater",
                    "name" => "some_more_content",
                    "sub_fields" => array(
                        array(
                            "name" => "text",
                            "type" => "supertags",
                            "vocabs" => array("block"),
                            "multiple" => 1
                        ),
                        array(
                            "name" => "some_location",
                            "type" => "supertags",
                            "vocabs" => array("location"),
                            "multiple" => 0
                        )
                    )
                ),

                // region more content
                array(
                    "type" => "repeater",
                    "name" => "main_content",
                    "sub_fields" => array(
                        array(
                            "name" => "division",
                            "type" => "supertags",
                            "vocabs" => array("division"),
                            "multiple" => 0
                        )
                    )
                )
                
            )
        );

        $this->testClass = new Supertags(array(
            "contentTypes" => $contentTypes
        ));

        parent::setup();
    }

    public function testFindRelationships()
    {
        $result = $this->testClass->findRelationships();

        $expected = array(
            "post" => array(
                "block" => array(
                    array(
                        "name" => "text",
                        "multiple" => 1,
                        "parent" => "some_more_content",
                        "onlyChild" => false
                    )
                ),
                "profile" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1,
                        "parent" => null,
                        "onlyChild" => false
                    ),
                    array(
                        "name" => "profile",
                        "multiple" => 0,
                        "parent" => null,
                        "onlyChild" => false
                    )
                ),
                "person" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1,
                        "parent" => null,
                        "onlyChild" => false
                    )
                ),
                "location" => array(
                    array(
                        "name" => "connections",
                        "multiple" => 1,
                        "parent" => null,
                        "onlyChild" => false
                    ),
                    array(
                        "name" => "some_location",
                        "multiple" => 0,
                        "parent" => "some_more_content",
                        "onlyChild" => false
                    )
                ),
                "division" => array(
                    array(
                        "name" => "division",
                        "multiple" => 0,
                        "parent" => "main_content",
                        "onlyChild" => true
                    )
                )
            )
        );

        $this->assertEquals($expected, $result);
    }

    public function testCleanMeta()
    {
        $given = array(
            "main_content" => array(
                array(10420),
                array(8712)
            ),
            "some_more_content" => array(
                array(
                    "text" => array(10420, 10455),
                    "some_location" => array(7966)
                ),
                array(
                    "text" => array(10420),
                    "some_location" => array(7966)
                )
            ),
            "connections" => array(1234, 456),
            "profile" => array(1234)

        );
        $expected = array(
            "main_content" => array(10420, 8712),
            "some_more_content" => array(
                array(
                    "text" => array(10420, 10455),
                    "some_location" => 7966
                ),
                array(
                    "text" => array(10420),
                    "some_location" => 7966
                )
            ),
            "connections" => array(1234, 456),
            "profile" => 1234
        );

        $result = $this->testClass->cleanMeta($given, "post");
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

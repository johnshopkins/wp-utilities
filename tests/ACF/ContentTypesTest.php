<?php
namespace WPUtilities\ACF;

class ContentTypesTest extends \WPUtilities\BaseTest
{
    protected $fieldGroups;

    protected $rules = array();
    protected $wordpress_meta = array();
    protected $fieldGroupPosts = array();
    protected $cleanedFieldGroups = array();

    public function setUp()
    {
        $this->makeVars();

        $this->testClass = new ContentTypes(array(
            "wordpress" => $this->getWordPress(),
            "wordpress_query" => $this->getWordPressQuery()
        ));

        parent::setup();
    }

    public function testFind()
    {
        $result = $this->testClass->find();

        $expected = array(
            "post" => $this->cleanedFieldGroups[0]["fields"],
            "page" => array()
        );

        $this->assertEquals($expected, $result);
    }

    public function testCleanMeta()
    {
        $given = array(
            "first_name" => "jen",
            "supertag_multi_content" => array(1234, 5678),
            "supertag_single_content" => array(1234),
            "profile_image" => 1234,
            "hobbies_0_hobby" => "Baseball",
            "hobbies_1_hobby" => "Football",
            "people_0_person" => array(1234),
            "location_ratings_0_location" => array(1234),
            "location_ratings_0_rating" => "5 stars",
            "location_ratings_1_location" => array(5678),
            "location_ratings_1_rating" => "4 stars",
            "names_0_first_name" => "jen",
            "names_0_last_name" => "wachter",
            "names_1_first_name" => "jane",
            "names_1_last_name" => "doe"


        );
        $expected = array(
            "first_name" => "jen",
            "supertag_multi_content" => array(
                "http://local.jhu.edu/api/1234/",
                "http://local.jhu.edu/api/5678/"
            ),
            "supertag_single_content" => "http://local.jhu.edu/api/1234/",
            "profile_image" => 1234,
            "hobbies" => array("Baseball", "Football"),
            "people" => array(
                "http://local.jhu.edu/api/1234/"
            ),
            "location_ratings" => array(
                array(
                    "location" => "http://local.jhu.edu/api/1234/",
                    "rating" => "5 stars"
                ),
                array(
                    "location" => "http://local.jhu.edu/api/5678/",
                    "rating" => "4 stars"
                )
            ),
            "names" => array(
                array(
                    "first_name" => "jen",
                    "last_name" => "wachter"
                ),
                array(
                    "first_name" => "jane",
                    "last_name" => "doe"
                )
            )
        );

        $result = $this->testClass->cleanMeta($given, "post");
        $this->assertEquals($expected, $result);
    }

    protected function makeVars()
    {
        $rules = array(
            array(
                "param" => "post_type",
                "operator" => "==",
                "value" => "post"
            ),
            array(
                "param" => "not_post_type"
            )
        );

        $this->wordpress_meta = array(

            // some other meta, not a field
            "not_a_field" => "not_a_field",

            // standard text
            "field_5376326d599df" => array(
                serialize(array(
                    "type" => "text",
                    "name" => "first_name"
                ))
            ),

            // supertags (multiple)
            "field_5376326d599da" => array(
                serialize(array(
                    "type" => "supertags",
                    "name" => "supertag_multi_content",
                    "vocabs" => array("block", "field_of_study"),
                    "multiple" => 1
                ))
            ),

            // supertags (single)
            "field_5376326d5991b" => array(
                serialize(array(
                    "type" => "supertags",
                    "name" => "supertag_single_content",
                    "vocabs" => array("location"),
                    "multiple" => 0
                ))
            ),

            // file
            "field_5376326d5995b" => array(
                serialize(array(
                    "type" => "file",
                    "name" => "profile_image"
                ))
            ),

            // repeater (one subfield)
            "field_5376326d5997b" => array(
                serialize(array(
                    "type" => "repeater",
                    "name" => "hobbies",
                    "sub_fields" => array(
                        array(
                            "type" => "text",
                            "name" => "hobby"
                        )
                    )
                ))
            ),

            // repeater (one subfield as supertag)
            "field_5376326d5993b" => array(
                serialize(array(
                    "type" => "repeater",
                    "name" => "people",
                    "sub_fields" => array(
                        array(
                            "type" => "supertags",
                            "name" => "person",
                            "vocabs" => array("person"),
                            "multiple" => 0
                        )
                    )
                ))
            ),

            // repeater (multiple subfields, one as supertag)
            "field_5376326d5992b" => array(
                serialize(array(
                    "type" => "repeater",
                    "name" => "location_ratings",
                    "sub_fields" => array(
                        array(
                            "type" => "supertags",
                            "name" => "location",
                            "vocabs" => array("location"),
                            "multiple" => 0
                        ),
                        array(
                            "type" => "text",
                            "name" => "rating"
                        )
                    )
                ))
            ),

            // repeater (multiple subfields)
            "field_5376326d594db" => array(
                serialize(array(
                    "type" => "repeater",
                    "name" => "names",
                    "sub_fields" => array(
                        array(
                            "type" => "text",
                            "name" => "first_name"
                        ),
                        array(
                            "type" => "text",
                            "name" => "last_name"
                        )
                    )
                ))
            ),
            
            "rule" => array_map(function ($rule) {
                return serialize($rule);
            }, $rules)
        );


        $group = new \StdClass();
        $group->ID = 1;
        $this->fieldGroupPosts = array($group);

        $this->cleanedFieldGroups = array(
            array(
                "rules" => $rules,
                "fields" => array(

                    // standard text
                    "first_name" => array(
                        "type" => "text",
                        "name" => "first_name"
                    ),

                    // supertags (multiple)
                    "supertag_multi_content" => array(
                        "type" => "supertags",
                        "name" => "supertag_multi_content",
                        "vocabs" => array("block", "field_of_study"),
                        "multiple" => 1
                    ),

                    // supertags (single)
                    "supertag_single_content" => array(
                        "type" => "supertags",
                        "name" => "supertag_single_content",
                        "vocabs" => array("location"),
                        "multiple" => 0
                    ),

                    // file
                    "profile_image" => array(
                        "type" => "file",
                        "name" => "profile_image"
                    ),

                    // repeater (one subfield)
                    "hobbies" => array(
                        "type" => "repeater",
                        "name" => "hobbies",
                        "sub_fields" => array(
                            array(
                                "type" => "text",
                                "name" => "hobby"
                            )
                        )
                    ),

                    // repeater (one subfield as supertag)
                    "people" => array(
                        "type" => "repeater",
                        "name" => "people",
                        "sub_fields" => array(
                            array(
                                "type" => "supertags",
                                "name" => "person",
                                "vocabs" => array("person"),
                                "multiple" => 0
                            )
                        )
                    ),

                    // repeater (multiple subfields, one as supertag)
                    "location_ratings" => array(
                        "type" => "repeater",
                        "name" => "location_ratings",
                        "sub_fields" => array(
                            array(
                                "type" => "supertags",
                                "name" => "location",
                                "vocabs" => array("location"),
                                "multiple" => 0
                            ),
                            array(
                                "type" => "text",
                                "name" => "rating"
                            )
                        )
                    ),

                    // repeater (multiple subfields)
                    "names" => array(
                        "type" => "repeater",
                        "name" => "names",
                        "sub_fields" => array(
                            array(
                                "type" => "text",
                                "name" => "first_name"
                            ),
                            array(
                                "type" => "text",
                                "name" => "last_name"
                            )
                        )
                    )
                )
            )
        );
    }

    protected function getWordPress()
    {   
        $wordpress = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $wordpress->expects($this->any())
            ->method("__call")
            ->will($this->returnValueMap(array(
                array("get_post_types", array(array("public" => true)), array("post" => "post", "page" => "page")),
                array("get_post_meta", array(1), $this->wordpress_meta)
            )));

        return $wordpress;
    }

    protected function getWordPressQuery()
    {   
        $wordpress_query = $this->getMockBuilder("\\WPUtilities\\WPQueryWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $result = new \StdClass();
        $result->posts = $this->fieldGroupPosts;

        $wordpress_query->expects($this->any())
            ->method("run")
            ->will($this->returnValue($result));

        return $wordpress_query;
    }
}

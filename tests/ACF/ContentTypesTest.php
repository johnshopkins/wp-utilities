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
            "relationship_multi_content" => array(1234, 5678),
            "relationship_single_content" => array(1234)
        );
        $expected = array(
            "relationship_multi_content" => array(
                "http://local.jhu.edu/api/1234/",
                "http://local.jhu.edu/api/5678/"
            ),
            "relationship_single_content" => "http://local.jhu.edu/api/1234/"
        );

        $result = $this->testClass->cleanMeta($given, "post", 100);
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

            // relationship (multiple)
            "field_5376326d599da" => array(
                serialize(array(
                    "type" => "relationship",
                    "name" => "relationship_multi_content",
                    "post_type" => array("block", "field_of_study"),
                    "max" => ""
                ))
            ),

            // relationship (single)
            "field_5376326d5991b" => array(
                serialize(array(
                    "type" => "relationship",
                    "name" => "relationship_single_content",
                    "post_type" => array("location"),
                    "max" => 1
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

                    // relationship (multiple)
                    "relationship_multi_content" => array(
                        "type" => "relationship",
                        "name" => "relationship_multi_content",
                        "post_type" => array("block", "field_of_study"),
                        "max" => ""
                    ),

                    // relationship (single)
                    "relationship_single_content" => array(
                        "type" => "relationship",
                        "name" => "relationship_single_content",
                        "post_type" => array("location"),
                        "max" => 1
                    )
                )
            )
        );
    }

    public function testFindRelationships()
    {
        $expected = array(
            "post" => array(
                "block" => array(
                    array(
                        "name" => "relationship_multi_content",
                        "multiple" => 1,
                        "parent" => null,
                        "onlyChild" => false
                    )
                ),
                "field_of_study" => array(
                    array(
                        "name" => "relationship_multi_content",
                        "multiple" => 1,
                        "parent" => null,
                        "onlyChild" => false
                    )
                ),
                "location" => array(
                    array(
                        "name" => "relationship_single_content",
                        "multiple" => 0,
                        "parent" => null,
                        "onlyChild" => false
                    )
                )
            ),
            "page" => array()
        );

        $result = $this->testClass->findRelationships();
        $this->assertEquals($expected, $result);
    }

    protected function getWordPress()
    {   
        $wordpress = $this->getMockBuilder("\\WPUtilities\\WordPressWrapper")
            ->disableOriginalConstructor()
            ->getMock();

        $wordpress->expects($this->any())
            ->method("__call")
            ->will($this->returnValueMap(array(
                array("get_post_types", array(array("show_in_menu" => "content")), array("post" => "post", "page" => "page")),
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

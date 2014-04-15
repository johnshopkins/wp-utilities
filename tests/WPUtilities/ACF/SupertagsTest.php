<?php
namespace tests\WPUtilities\ACF;

class SupertagsTest extends \tests\Base
{
    protected $fieldGroups;

    protected $rules = array();
    protected $wordpress_meta = array();
    protected $fieldGroupPosts = array();
    protected $cleanedFieldGroups = array();

    public function setUp()
    {
        $this->makeVars();

        $this->testClass = new \WPUtilities\ACF\Supertags(array(
            "wordpress" => $this->getWordPress(),
            "wordpress_query" => $this->getWordPressQuery()
        ));

        parent::setup();
    }

    public function testFind()
    {
        $result = $this->testClass->find();

        $expected = array(
            "person" => $this->cleanedFieldGroups[0]["supertags"],
            "something_else" => $this->cleanedFieldGroups[0]["supertags"]
        );

        $this->assertEquals($expected, $result);
    }

    public function testGetFieldGroups()
    {
        $method = $this->getMethod("getFieldGroups");
        $result = $method->invoke($this->testClass);
        
        $this->assertEquals($this->fieldGroupPosts, $result);
    }

    public function testFindSupertagMeta()
    {
        $method = $this->getMethod("findSupertagMeta");

        $expected = array(
            "location" => array(
                array(
                    "name" => "campus_stuff",
                    "multiple" => true
                ),
                array(
                    "name" => "campus_location",
                    "multiple" => false
                )
            ),
            "division" => array(
                array(
                    "name" => "campus_stuff",
                    "multiple" => true
                )
            )
        );

        $result = $method->invoke($this->testClass, $this->wordpress_meta);
        $this->assertEquals($expected, $result);

    }

    public function testFindGroupsWithSupertags()
    {
        $method = $this->getMethod("findGroupsWithSupertags");
        
        $result = $method->invoke($this->testClass);
        $this->assertEquals($this->cleanedFieldGroups, $result);
    }

    public function testFindPostTypeRelations()
    {
        $method = $this->getMethod("findPostTypeRelations");

        $expected = array(
            "person" => $this->cleanedFieldGroups[0]["supertags"],
            "something_else" => $this->cleanedFieldGroups[0]["supertags"]
        );

        $result = $method->invoke($this->testClass, $this->cleanedFieldGroups);
        $this->assertEquals($expected, $result);
    }



    protected function makeVars()
    {
        $rules = array(
            array(
                "param" => "post_type",
                "operator" => "==",
                "value" => "person"
            ),
            array(
                "param" => "post_type",
                "operator" => "==",
                "value" => "something_else"
            ),
            array(
                "param" => "not_post_type"
            )
        );

        $this->wordpress_meta = array(
            "notField" => "notField",
            "field_123" => array(
                serialize(array(
                    "type" => "notSupertags"
                ))
            ),
            "field_456" => array(
                serialize(array(
                    "type" => "supertags",
                    "name" => "campus_stuff",
                    "vocabs" => array("location", "division"),
                    "multiple" => 1
                ))
            ),
            "field_789" => array(
                serialize(array(
                    "type" => "supertags",
                    "name" => "campus_location",
                    "vocabs" => array("location"),
                    "multiple" => 0
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
                "supertags" => array(
                    "division" => array(
                        array(
                            "name" => "campus_stuff",
                            "multiple" => 1
                        )
                    ),
                    "location" => array(
                        array(
                            "name" => "campus_stuff",
                            "multiple" => 1
                        ),
                        array(
                            "name" => "campus_location",
                            "multiple" => 0
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
                array("get_post_types", array(array("public" => true)), array("person" => "person", "something_else" => "something_else")),
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
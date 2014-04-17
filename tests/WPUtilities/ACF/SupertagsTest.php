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
        $this->testClass = new \WPUtilities\ACF\Supertags(array(
            "acf_contentTypes" => $this->getContentTypes()
        ));

        parent::setup();
    }

    public function testFind()
    {
        $result = $this->testClass->find();

        $expected = array(
            "person" => array(
                "location" => array(
                    array(
                        "name" => "campus_stuff",
                        "multiple" => 1
                    ),
                    array(
                        "name" => "campus_location",
                        "multiple" => 0
                    )
                ),
                "division" => array(
                    array(
                        "name" => "campus_stuff",
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
<?php
namespace tests\WPUtilities;

class PostTest extends \tests\Base
{
    protected $meta = array(
        "name" => array("john", "jane", "james"),
        "city" => array("baltimore"),
        "location" => "a:1:{i:0;i:7939;}",
        "_hidden" => "hidden stuff"
    );

    public function setUp()
    {
        $this->testClass = new \WPUtilities\Post(array(
            "wordpress" => $this->getWordPress(),
            "repeater" => $this->getAcf()
        ));
        parent::setup();
    }

    public function testGetMeta()
    {
        $expected = array(
            "name" => array("john", "jane", "james"),
            "city" => "baltimore",
            "location" => array(7939)
        );

        $result = $this->testClass->getMeta(10);
        $this->assertEquals($expected, $result);
    }

    public function testGetTags()
    {
        $expected = array("astronomy", "space");

        $result = $this->testClass->getTags(10);
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
                array("get_post_meta", array(10), $this->meta),
                array("maybe_unserialize", array(array("john", "jane", "james")), array("john", "jane", "james")),
                array("maybe_unserialize", array("baltimore"), "baltimore"),
                array("maybe_unserialize", array("a:1:{i:0;i:7939;}"), array(7939)),
                array("maybe_unserialize", array("hidden stuff"), "hidden stuff"),
                array("get_the_tags", array(10), $this->createTagsArray())
            )));

        return $wordpress;
    }

    protected function getAcf()
    {   
        $repeater = $this->getMockBuilder("\\WPUtilities\\ACF\\Repeater")
            ->disableOriginalConstructor()
            ->getMock();

        return $repeater;
    }

    protected function createTagsArray()
    {
        $tags = array();
        $tag1 = new \StdClass();
        $tag1->name = "astronomy";
        $tag1->slug = "slug";
        $tags[] = $tag1;

        $tag2 = new \StdClass();
        $tag2->name = "space";
        $tag2->slug = "slugy";
        $tags[] = $tag2;

        return $tags;
    }
}
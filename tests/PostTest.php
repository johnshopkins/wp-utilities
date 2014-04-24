<?php
namespace WPUtilities;

class PostTest extends BaseTest
{
    protected $meta = array(
        "name" => array("john", "jane", "james"),
        "city" => array("baltimore"),
        "location" => "a:1:{i:0;i:7939;}",
        "_hidden" => "hidden stuff"
    );

    public function setUp()
    {
        $this->testClass = new Post(array(
            "wordpress" => $this->getWordPress(),
            "acf_contentTypes" => $this->getAcfContentTypes()
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

        $result = $this->testClass->getMeta(10, "post");
        $this->assertEquals($expected, $result);
    }

    public function testGetTags()
    {
        $expected = array("astronomy", "space");

        $result = $this->testClass->getTags(10);
        $this->assertEquals($expected, $result);

        $result = $this->testClass->getTags(20);
        $this->assertEquals(array(), $result);
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
                array("get_the_tags", array(10), $this->createTagsArray()),
                array("get_the_tags", array(20), false)
            )));

        return $wordpress;
    }

    protected function getAcfContentTypes()
    {   
        $contentTypes = $this->getMockBuilder("\\WPUtilities\\ACF\\ContentTypes")
            ->disableOriginalConstructor()
            ->getMock();

        $contentTypes->expects($this->any())
            ->method("cleanMeta")
            ->will($this->returnValue(array(
                "name" => array("john", "jane", "james"),
                "city" => "baltimore",
                "location" => array(7939),
                "_hidden" => "hidden stuff"
            )));

        return $contentTypes;
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
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

    public function testsIsRevision()
    {
        $isRevision = new \StdClass();
        $isRevision->ID = 10;
        $result = $this->testClass->isRevision($isRevision);
        $this->assertTrue($result);

        $isRevision->post_type = "revision";
        $result = $this->testClass->isRevision($isRevision);
        $this->assertTrue($result);

        $notRevision = new \StdClass();
        $notRevision->ID = 20;
        $notRevision->post_type = "post";
        $result = $this->testClass->isRevision($notRevision);
        $this->assertFalse($result);
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

    public function testGetTerms()
    {
        $expected = array("astronomy", "space");

        $result = $this->testClass->getTerms(10, "post_tag");
        $this->assertEquals($expected, $result);

        $result = $this->testClass->getTerms(20, "post_tag");
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
                array("wp_get_post_terms", array(10, "post_tag"), $this->createTagsArray()),
                array("wp_get_post_terms", array(20, "post_tag"), array()),
                array("wp_is_post_revision", array(10), 100),
                array("wp_is_post_revision", array(20), false)
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

<?php
namespace WPUtilities;

class APITest extends BaseTest
{
    public function setUp()
    {
        $this->testClass = new API();
        parent::setup();
    }

    public function testGetApiBase()
    {
        $method = $this->getMethod("getApiBase");

        $result = $method->invoke($this->testClass, "production");
        $this->assertEquals("http://jhu.edu/api", $result);

        $result = $method->invoke($this->testClass, "staging");
        $this->assertEquals("http://staging.jhu.edu/api", $result);

        $result = $method->invoke($this->testClass, "local");
        $this->assertEquals("http://local.jhu.edu/api", $result);
    }
}
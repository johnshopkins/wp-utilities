// <?php

// namespace WPUtilities\Theme\Transformers;

// class BaseTest extends \WPUtilities\BaseTest
// {

//   public function setUp()
//   {
//     $this->testClass = new Base(array(
//       "api" => $this->getApi(),
//       "postUtil" => $this->getPostUtil(),
//       "contentTypes" => $this->getContentTypes()
//     ));
//     parent::setup();
//   }

//   protected function getApi()
//   {
//     $api = $this->getMockBuilder("\\WPUtilities\\API")
//       ->disableOriginalConstructor()
//       ->getMock();

//     return $api;
//   }

//   protected function getPostUtil()
//   {
//     $postUtil = $this->getMockBuilder("\\WPUtilities\\Post")
//       ->disableOriginalConstructor()
//       ->getMock();

//     $postUtil->expects($this->any())
//       ->method("getMeta")
//       ->will($this->returnValue("meta"));

//     return $postUtil;
//   }

//   protected function getContentTypes()
//   {   
//     $contentTypes = $this->getMockBuilder("\\WPUtilities\\ACF\\ContentTypes")
//       ->disableOriginalConstructor()
//       ->getMock();

//     return $contentTypes;
//   }

// }

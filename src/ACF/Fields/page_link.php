<?php

namespace WPUtilities\ACF\Fields;

class page_link extends Base
{

  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    parent::__construct($fieldData, $id, $parent, $deps);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);

    if (empty($value) || $value == "null") return null;

    return $this->getApiUrl($value);
  }
  
}

<?php

namespace WPUtilities\ACF\Fields;

class related_content_picker extends Base
{
  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    parent::__construct($fieldData, $id, $parent, $deps);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return null;

    if ($value == "inherit") {
      $value = $this->findParent("region_related_sidebar");
    }

    return $value ? $this->getApiUrl($value) : null;
    
  }

}

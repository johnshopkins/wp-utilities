<?php

namespace WPUtilities\ACF\Fields;

class related_content_picker extends Base
{
  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return null;

    if ($value == "inherit") {
      $value = $this->findParent("sidebar");
    }

    if ($value) {
      $value = $this->getApiUrl($value);
    }

    return $value;
  }

}

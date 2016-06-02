<?php

namespace WPUtilities\ACF\FieldCleaners;

class related_content_picker extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) return null;

    if ($value == "inherit") {
      $value = $this->findParent("region_related_sidebar");
    }

    return $value ? $this->getApiUrl($value) : null;

  }

}

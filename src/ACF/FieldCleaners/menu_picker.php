<?php

namespace WPUtilities\ACF\FieldCleaners;

class menu_picker extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) return null;

    if ($value == "inherit") {
      $value = $this->findParent("menu");
    }

    return $value ? $value : null;
  }

}

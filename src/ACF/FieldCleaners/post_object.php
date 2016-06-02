<?php

namespace WPUtilities\ACF\FieldCleaners;

class post_object extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value) || $value == "null") return null;

    return $this->getApiUrl($value);
  }

}

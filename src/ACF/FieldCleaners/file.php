<?php

namespace WPUtilities\ACF\FieldCleaners;

class file extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) return null;

    return $this->getApiUrl($value);
  }

}

<?php

namespace WPUtilities\ACF\FieldCleaners;

class jhu_color_picker extends Base
{
  protected function getValue()
  {
    $value = parent::getValue();
    return json_decode($value);
  }

}

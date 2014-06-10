<?php

namespace WPUtilities\ACF\Fields;

class jhu_color_picker extends Base
{
  public function __construct($fieldData, $id, $parent = null)
  {
    parent::__construct($fieldData, $id, $parent);
  }

  protected function getValue($meta)
  {
    $value = parent::getValue($meta);

    // $value is an object, which empty() does not work
    // on. Cast as an array and then check value.
    $asArray = (array) $value;
    if (empty($asArray)) return null;

    return $value;
  }

}
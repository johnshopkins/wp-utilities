<?php

namespace WPUtilities\ACF\Fields;

class repeater extends Base
{
  protected $subfields;

  public function __construct($fieldData)
  {
    parent::__construct($fieldData);
    $this->subfields = $fieldData["sub_fields"];
  }

  public function assemble($meta)
  {
    // print_r($this->fieldData);
    // print_r($meta); die();

    $repeaterMeta = array();

    $subfieldNames = array_map(function ($subfield) {
      return $subfield["name"];
    }, $this->subfields);

    $regex = "/^" . $this->fieldName . "_(\d+)_(" . implode("|", $subfieldNames) .")$/";


    // echo $regex;
    // print_r($meta);
    // die();

      foreach ($meta as $key => $value) {

        if (preg_match($regex, $key, $matches)) {

          // this metadata key matches the repeater pattern!
          
          $repeaterMeta[$this->fieldName] = array();

          $index = $matches[1];       // ordered location
          $subfield = $matches[2];    // subfield name

          if (count($this->subfields) > 1) {

            // there more than one subfield in this array, so nest
            // the data in the appropiate subfield

            $meta[$fieldName][$index][$subfield] = $value;
          } else {

            // there is only one subfield in this array, so don't
            // nest the data in an array

            $meta[$this->fieldName][$index] = $value;
          }
          
          // unset the crazy ACF meta key
          $this->usedKeys[] = $meta[$key];

        }
    }

    return $repeaterMeta;
  }
}

<?php

namespace WPUtilities\ACF\Fields;

class repeater extends Base
{
  protected $subfields;

  public function __construct($fieldData, $parent = null)
  {
    parent::__construct($fieldData, $parent);
    $this->subfields = $fieldData["sub_fields"];
  }

  public function getValue($meta)
  {
    // nested repeaters. not supported yet.
  }

  public function assemble($meta)
  {
    $repeaterMeta = array(
      $this->fieldName => array()
    );

    $subfieldNames = array_map(function ($subfield) {
      return $subfield["name"];
    }, $this->subfields);

    $regex = "/^" . $this->fieldName . "_(\d+)_(" . implode("|", $subfieldNames) .")$/";

      foreach ($meta as $key => $value) {

        if (preg_match($regex, $key, $matches)) {

          // this metadata key matches the repeater pattern!
          
          // $repeaterMeta[$this->fieldName] = array();

          $index = $matches[1];       // ordered location
          $subfield = $matches[2];    // subfield name

          
          // get subfield value
          $subfieldIndex = array_search($subfield, $subfieldNames);
          $subfieldDetails = $this->subfields[$subfieldIndex];
          $className = "WPUtilities\\ACF\\Fields\\{$subfieldDetails['type']}";
          $className = class_exists($className) ? $className : "WPUtilities\\ACF\\Fields\\Base";

          $fieldHelper = new $className($subfieldDetails, "{$this->fieldName}_{$index}");

          // using the given meta, assemble together the field
          $value = $fieldHelper->getValue($meta);

          $this->usedKeys = array_merge($this->usedKeys, $fieldHelper->usedKeys);

          if ($this->fieldData["row_limit"] == 1) {
            
            // this repeater is limited to one row of data
            $repeaterMeta[$this->fieldName][$subfield] = $value;

          } else if (count($this->subfields) > 1) {

            // there more than one subfield in this array, so nest
            // the data in the appropiate subfield

            $repeaterMeta[$this->fieldName][$index][$subfield] = $value;
          } else {

            // there is only one subfield in this array, so don't
            // nest the data in an array

            $repeaterMeta[$this->fieldName][$index] = $value;
          }
          
          // unset the crazy ACF meta key
          $this->usedKeys[] = $key;

        }
    }

    return $repeaterMeta;
  }
}

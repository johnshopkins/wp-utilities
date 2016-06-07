<?php

namespace WPUtilities\ACF\FieldCleaners;

class repeater extends Base
{
  public function clean()
  {
    if (empty($this->field["value"])) return array();

    $subfields = $this->getSubfields();

    foreach ($this->field["value"] as &$row) {

      foreach ($row as $fieldName => &$fieldValue) {

        // clean subfields

        $subfield = $subfields[$fieldName];
        $subfield["value"] = $fieldValue; // add value to mimic top-level fields

        $type = $subfield["type"];

        $className = "WPUtilities\\ACF\\FieldCleaners\\{$type}";
        $className = class_exists($className) ? $className : "WPUtilities\\ACF\\FieldCleaners\\Base";

        $fieldCleaner = new $className($subfield, $this->id);

        $fieldValue = $fieldCleaner->clean();

      }

      if (count($subfields) == 1) {
        $row = array_shift($row);
      }

    }

    if ($this->field["max"] == 1) {
      return array_shift($this->field["value"]);
    } else {
      return $this->field["value"];
    }
  }

  /**
   * Get the subfields of this repeater field
   * @return array
   */
  protected function getSubfields()
  {
    $subfields = array();

    // assign field name to subfield key for easy access
    foreach ($this->field["sub_fields"] as $subfield) {
      $subfields[$subfield["name"]] = $subfield;
    }

    return $subfields;
  }
}

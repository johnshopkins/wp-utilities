<?php

namespace WPUtilities\Theme\Transformers;

class Page extends Base
{

  /**
   * Take an array of content assigned to a
   * given region (given as IDs) and return
   * the associated objects.
   * @param  array $ids Region content IDs
   * @return array Region objects
   */
  public function compileRegion($ids)
  {
    return array_map(function ($array) {

      // Regions are supertags and all data is stored as
      // an array, even if each field only accepts one supertag
      $id = array_shift($array);

      return $this->api->get("/{$id}")->data;

    }, $ids);
  }

}

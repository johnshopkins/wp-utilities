<?php

namespace WPUtilities\ACF\Fields;

class wysiwyg extends Base
{
  protected $multiple;

  public function __construct($fieldData, $id, $parent = null, $deps = array())
  {
    parent::__construct($fieldData, $id, $parent, $deps);
  }

  public function getValue($meta)
  {
    $value = parent::getValue($meta);
    if (empty($value)) return null;

    $value = $this->wordpress->do_shortcode($value);
    $value = $this->wordpress->wpautop($value);
    $value = $this->absoluteToRelativeLinks($value);

    return $value;
  }

  protected function absoluteToRelativeLinks($value)
  {
    $regex = $this->getDomainRegex();
    return preg_replace($regex, "/", $value);
  }

  protected function getDomainRegex()
  {
    // // get the home url, without http:// or https://
    // $home_url = $this->wordpress->get_home_url();
    // $home_url = str_replace(array("http://", "https://"), "", $home_url);

    // // return top level domain (ex: jhu.edu)
    // $parts = explode(".", $home_url);
    // $domain = implode(".", array_slice($parts, -2));

    // // match any subdomain
    // return "/http(s)?:\/\/([^\.].)*" . $domain . "(\/)?/";

    return "/http(s)?:\/\/(local|staging|beta|www)*.jhu.edu(\/)?/";
  }

}

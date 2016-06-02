<?php

namespace WPUtilities\ACF\FieldCleaners;

class wysiwyg extends Base
{
  public function getValue()
  {
    $value = parent::getValue();
    if (empty($value)) return null;

    $value = $this->wordpress->do_shortcode($value);
    $value = $this->wordpress->wpautop($value);
    $value = $this->absoluteToRelativeLinks($value);
    $value = $this->removeUnicodeNbsp($value);

    return $value;
  }

  protected function removeUnicodeNbsp($value)
  {
    return str_replace("\xc2\xa0", " ", $value);
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

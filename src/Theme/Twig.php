<?php

namespace WPUtilities\Theme;

class Twig
{
  protected $twig;

  public function __construct($templateDir, $deps = array())
  {
    $this->twig = isset($deps["twig"]) ? $deps["twig"] : new \Twig_Environment(new \Twig_Loader_Filesystem($templateDir));
  }

  public function display($template, $data)
  {
    return $this->twig->display($template, $data);
  }
}

<?php

namespace WPUtilities\Theme;

class Twig
{
  protected $twig;

  public function __construct($templateDir, $options = array(), $deps = array())
  {
    $this->twig = isset($deps["twig"]) ? $deps["twig"] : new \Twig_Environment(new \Twig_Loader_Filesystem($templateDir), $options);
  }

  public function display($template, $data)
  {
    return $this->twig->display($template, $data);
  }
}

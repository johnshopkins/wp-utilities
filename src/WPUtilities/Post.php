<?php

namespace WPUtilities;

class Post
{
    protected $wordpress;

    public function __construct()
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);
        
        $this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new WordPressWrapper();
        $this->repeater = isset($args["acf_repeater"]) ? $args["acf_repeater"] : new ACF\Repeater();
    }

    public function getMeta($id)
    {
        $meta = $this->wordpress->get_post_meta($id);
        $meta = $this->fixArrays($meta);
        $meta = $this->repeater->cleanMeta($meta);
        return $this->removeHiddenMeta($meta);
    }

    protected function fixArrays($meta)
    {
        foreach($meta as $k => &$v) {

            // if there is only one value, shift it out
            if (is_array($v) && count($v) == 1) {
                $v = array_shift($v);
            }

            // unserialize serialized arrays
            $v = $this->wordpress->maybe_unserialize($v);
            
        }

        return $meta;
    }

    protected function removeHiddenMeta($meta)
    {
        foreach($meta as $k => &$v) {

            if (preg_match("/^_/", $k)) {
                unset($meta[$k]);
            }
        }

        return $meta;
    }
}
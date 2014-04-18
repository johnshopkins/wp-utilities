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
        $this->contentTypes = isset($args["acf_contentTypes"]) ? $args["acf_contentTypes"] : new ACF\ContentTypes();
    }

    public function getMeta($id, $postType)
    {
        $meta = $this->wordpress->get_post_meta($id);
        $meta = $this->fixArrays($meta);
        $meta = $this->contentTypes->cleanMeta($meta, $postType);
        return $this->removeHiddenMeta($meta);
    }

    public function getTags($id)
    {
        $tags = $this->wordpress->get_the_tags($id);
        if (!$tags) return array();
        return array_map(function ($tag) {
            return $tag->name;
        }, $tags);
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
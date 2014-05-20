<?php

namespace WPUtilities;

class Post
{
    protected $wordpress;
    protected $contentTypes = array();

    public function __construct($deps = array())
    {
        $this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new WordPressWrapper();
        $this->contentTypes = isset($deps["acf_contentTypes"]) ? $deps["acf_contentTypes"] : new ACF\ContentTypes();
    }

    /**
     * Figures out if a given post is a revision. Using wp_is_post_revision()
     * alone doesn't seem to catch all revisions. When deleting a post with
     * multiple revisions from edit.php, sometimes a post will return false
     * from wp_is_post_revision, even though it has a post type of revision.
     * 
     * @param  object  $post Post
     * @return boolean TRUE if revision, FALSE if not a revision
     */
    public function isRevision($post)
    {
        $revision = $this->wordpress->wp_is_post_revision($post->ID);
        return $revision || $post->post_type == "revision";
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

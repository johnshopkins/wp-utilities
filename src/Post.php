<?php

namespace WPUtilities;

class Post
{
    protected $wordpress;
    protected $contentTypes = array();
    protected $children = array();

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

    /**
     * Get children of a post
     * @param integer $id Post ID
     * @param boolean $id Return a flat array of IDs (TRUE)
     *                    or return multidimensional array of
     *                    posts that preserves hierarchy (FALSE)
     */
    public function getChildren($id, $returnFlat = true)
    {
      $children = $this->getChildrenOfPost($id);
      return $returnFlat ? $this->children : $children;
    }

    protected function getChildrenOfPost($id)
    {
      $children = get_children(array(
        "post_parent" => $id,
        "post_type" => "page"
      ));

      foreach ($children as &$child) {

        $id = $child->ID;
        $this->children[] = $id;
        $child->children = $this->getChildrenOfPost($id);
      }

      return $children;
    }

    public function getMeta($id, $postType)
    {
        $meta = $this->wordpress->get_post_meta($id);
        $meta = $this->fixArrays($meta);
        $meta = $this->contentTypes->cleanMeta($meta, $postType, $id);
        return $this->removeHiddenMeta($meta);
    }

    public function getTerms($id, $taxonomy)
    {
        $terms = $this->wordpress->wp_get_post_terms($id, $taxonomy);
        return array_map(function ($term) {
            return $term->name;
        }, $terms);
    }

    public function getTermObjects($id, $taxonomy)
    {
        return $this->wordpress->wp_get_post_terms($id, $taxonomy);
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

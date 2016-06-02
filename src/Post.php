<?php

namespace WPUtilities;

class Post
{
    protected $wordpress;
    protected $children = array();

    public function __construct()
    {
        $this->wordpress = new WordPressWrapper();
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

    public function getMeta($id)
    {
        $meta = array();

        // includes info about the field in addition to the value
        $fields = get_field_objects($id);

        if (!$fields) {
          // no advanced custom fields (ex: image)
          return array();
        }

        foreach ($fields as $field) {

            // relationship, image, etc...
            $type = $field["type"];

            $className = "\\WPUtilities\\ACF\\FieldCleaners\\{$type}";
            $className = class_exists($className) ? $className : "\\WPUtilities\\ACF\\FieldCleaners\\Base";

            $fieldCleaner = new $className($field, $id);

            $meta[$field["name"]] = $fieldCleaner->clean();

        }

        return $meta;
    }

    public function getTerms($id, $taxonomy)
    {
        $terms = $this->wordpress->wp_get_post_terms($id, $taxonomy);
        return array_map(function ($term) {
            return $term->name;
        }, $terms);
    }

    public function getTermObjects($id, $taxonomy, $fields = array())
    {
      $terms = $this->wordpress->wp_get_post_terms($id, $taxonomy);

      foreach ($terms as $term) {

        $term->parent = get_term_by("id", $term->parent, $taxonomy);

        foreach ($fields as $field) {
          $term->$field = get_field($field, $term);
        }

      }

      return $terms;
    }
}

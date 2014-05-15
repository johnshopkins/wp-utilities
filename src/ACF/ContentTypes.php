<?php

namespace WPUtilities\ACF;

/**
 * Creates an array of post types and thier associated fields. Each field has
 * data about its properties.
 *
 * We use Advanced Custom Fields [http://www.advancedcustomfields.com/] to manage
 * content types within WordPress. Whether it is an advantage or disdvantage, ACF
 * manages fields in Field Groups that can then be assigned to a post type.
 * Because of this, theres isn't an easy way to get all the fields assigned to a
 * particular field type.
 *
 * ACF stores the meta data for some fields (mainly repeater fields) in a strange,
 * unreadable way. You can use ACF functions to normalize the data, but since we
 * import a lot of our data, the ACF functions are useless to us (long story...).
 *
 * So we need to take it upon ourselves to clean this data. For this reason, we
 * need to know which fields are assigned to the particular kind of post type we
 * are cleaning up. Knowing the fields, we can find which fields are repeaters and
 * supertags and clean them appropiatly. Previously, we were looking at context
 * clues to see which fields were which, which didn't work out too well.
 * 
 */

class ContentTypes
{
    protected $query;
    protected $wordpress;

    public function __construct($deps = array())
    {
        $this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new \WPUtilities\WordPressWrapper();
        $this->wpquery_wrapper = isset($deps["wordpress_query"]) ? $deps["wordpress_query"] : new \WPUtilities\WPQueryWrapper();

        $groups = $this->findGroups();
        $this->contentTypes = $this->assignFieldsToContentTypes($groups);

        $options = array("contentTypes" => $this->contentTypes);
        $this->supertags = isset($deps["acf_supertags"]) ? $deps["acf_supertags"] : new Supertags($this->contentTypes);
        $this->repeater = isset($deps["acf_repeater"]) ? $deps["acf_repeater"] : new Repeater($this->contentTypes);
    }

    /**
     * Find all WordPress content types and
     * their associatied ACF fields.
     * @return array
     */
    public function find()
    {
        return $this->contentTypes;
    }

    /**
     * Clean metadata of all ACF weirdness
     * @param  array  $meta
     * @param  string $postType
     * @return array
     */
    public function cleanMeta($meta, $postType)
    {
        return $this->repeater->cleanMeta($meta, $postType);
    }

    /**
     * Find all supertag fields in all content types
     * @return array
     */
    public function findSupertags()
    {
        return $this->supertags->find();
    }

    protected function findGroups()
    {
        $params = array("post_type" => "acf", "posts_per_page" => -1);
        $groups = $this->wpquery_wrapper->run($params);

        return $this->findGroupMeta($groups->posts);
    }

    protected function findGroupMeta($groups)
    {
        return array_filter(array_map(function ($fieldGroup) {

            $group = array(
                "fields" => array(),
                "rules" => array()
            );

            $meta = $this->wordpress->get_post_meta($fieldGroup->ID);

            foreach ($meta as $k => $v) {
                if (preg_match("/^field_/", $k)) {
                    $v = unserialize(array_shift($v));
                    $group["fields"][] = $v;
                }
            }

            $group["rules"] = array_map(function ($rule) {
                return unserialize($rule);
            }, $meta["rule"]);

            return $group;

        }, $groups));
    }

    protected function assignFieldsToContentTypes($fieldGroups)
    {
        $postTypes = $this->wordpress->get_post_types(array("public" => true));
        $types = $postTypes;

        foreach ($postTypes as $type) {

            $types[$type] = array();

            foreach ($fieldGroups as $fieldGroup) {

                foreach ($fieldGroup["rules"] as $rule) {

                    if ($rule["param"] != "post_type") {
                        continue;
                    }

                    switch ($rule["operator"]) {
                        case "==":
                            if ($type == $rule["value"]) {

                                foreach ($fieldGroup["fields"] as $field) {
                                    $types[$type][] = $field;
                                }
                            }
                    }

                }

            }

        }

        return $types;
    }
}

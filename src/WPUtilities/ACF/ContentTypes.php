<?php

namespace WPUtilities\ACF;

class ContentTypes
{
    protected $query;
    protected $wordpress;

    public function __construct()
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);

        $this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
        $this->wpquery_wrapper = isset($args["wordpress_query"]) ? $args["wordpress_query"] : new \WPUtilities\WPQueryWrapper();

        $groups = $this->findGroups();
        $this->contentTypes = $this->assignFieldsToContentTypes($groups);

        $options = array("contentTypes" => $this->contentTypes);
        $this->supertags = isset($args["acf_supertags"]) ? $args["acf_supertags"] : new Supertags($options);
        $this->repeater = isset($args["acf_repeater"]) ? $args["acf_repeater"] : new Repeater($options);
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
        $types = array();

        foreach ($postTypes as $type) {

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
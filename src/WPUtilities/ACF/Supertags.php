<?php

namespace WPUtilities\ACF;

class Supertags
{
    protected $wordpress;
    protected $wpquery_wrapper;

    public function __construct()
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);

        $this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
        $this->wpquery_wrapper = isset($args["wordpress_query"]) ? $args["wordpress_query"] : new \WPUtilities\WPQueryWrapper();
    }

    public function find()
    {
        $supertaggedGroups = $this->findGroupsWithSupertags();
        return $this->findPostTypeRelations($supertaggedGroups);
    }

    /**
     * Find ACF field groups
     * @return array
     */
    protected function getFieldGroups()
    {
        $params = array("post_type" => "acf");
        $results = $this->wpquery_wrapper->run($params);

        return $results->posts;
    }

    /**
     * Find ACF field groups with supertag fields. Return
     * only the supertag fields and the rules associated
     * with the field group, as this is all we need to
     * map the field group to the post type.
     * @return array
     */
    protected function findGroupsWithSupertags()
    {
        $fieldGroups = $this->getFieldGroups();

        return array_filter(array_map(function ($fieldGroup) {

            $return = array();

            $meta = $this->wordpress->get_post_meta($fieldGroup->ID);

            $return["supertags"] = $this->findSupertagMeta($meta);

            if (empty($return["supertags"])) {
                return null;
            }

            $return["rules"] = array_map(function ($rule) {
                return unserialize($rule);
            }, $meta["rule"]);

            return $return;

        }, $fieldGroups));

    }

    /**
     * Loop through meta of a field group, find
     * fields that are supertags and which vocabs
     * these fields are associated with.
     * @param  array $meta Metadata
     * @return array Supertag fields
     */
    protected function findSupertagMeta($meta)
    {
        $supertags = array();

        foreach ($meta as $k => $v) {

            if (!preg_match("/^field_/", $k)) {
                continue;
            }


            $v = unserialize(array_shift($v));

            if ($v["type"] == "supertags") {

                foreach ($v["vocabs"] as $vocab) {
                    $supertags[$vocab][] = array(
                        "name" => $v["name"],
                        "multiple" => (bool) $v["multiple"]
                    );
                }
            }
            
        }

        return $supertags;
    }

    protected function findPostTypeRelations($fieldGroups)
    {
        $postTypes = $this->wordpress->get_post_types(array("public" => true));
        $typesWithSupertages = array();

        foreach ($postTypes as $type) {

            foreach ($fieldGroups as $fieldGroup) {

                foreach ($fieldGroup["rules"] as $rule) {

                    if ($rule["param"] != "post_type") {
                        continue;
                    }

                    switch ($rule["operator"]) {
                        case "==":
                            if ($type == $rule["value"]) {

                                if (!isset($typesWithSupertages[$type])) {
                                    $typesWithSupertages[$type] = array();
                                }

                                foreach ($fieldGroup["supertags"] as $vocab => $fields) {
                                    if (!isset($typesWithSupertages[$type][$vocab])) {
                                        $typesWithSupertages[$type][$vocab] = array();
                                    }

                                    foreach ($fields as $field) {
                                        $typesWithSupertages[$type][$vocab][] = $field;
                                    }
                                    
                                }
                            }
                    }

                }

            }

        }

        return $typesWithSupertages;
    }

}
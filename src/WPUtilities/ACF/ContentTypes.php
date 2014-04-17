<?php

namespace WPUtilities\ACF;

class ContentTypes
{
    protected $query;
    protected $wordpress;

    public function __construct()
    {
        $this->query = isset($args["query"]) ? $args["query"] : new Utilities\Query();
        $this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
    }

    public function getInfo($type)
    {
        $groups = $this->findGroups();
        print_r($groups); die();
    }

    protected findGroups()
    {
        $params = array("post_type" => "acf");
        $results = $this->wpquery_wrapper->run($params);

        return array_filter(array_map(function ($fieldGroup) {

            $return = array();

            $meta = $this->wordpress->get_post_meta($fieldGroup->ID);

            $return["rules"] = array_map(function ($rule) {
                return unserialize($rule);
            }, $meta["rule"]);

            return $return;

        }, $results->posts));
    }
}
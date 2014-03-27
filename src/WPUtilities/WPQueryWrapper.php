<?php

namespace WP\Utilities;

class WPQueryWrapper
{
    protected $query;

    public function run($args)
    {
        $this->query = new \WP_Query($args);
        return $this->query;
    }

    public function __get($prop)
    {
        if (property_exists($this->query, $prop)) {
            return $this->query->$prop;
        }
    }
 
    public function __call($method, $args)
    {
        if (method_exists($this->query, $method)) {
            return call_user_func_array(array($this->query, $method), $args);
        }
    }
}
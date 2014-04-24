<?php

namespace WPUtilities;

class API
{
    public static function getApiBase($env = null)
    {
        $env = is_null($env) ? ENV : $env;

        $prefix = "";

        if ($env != "production") {
            $prefix = $env . ".";
        }
        
        return "http://{$prefix}jhu.edu/api";
    }
}
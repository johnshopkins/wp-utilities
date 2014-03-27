<?php

namespace WPUtilities;

class WordPressWrapper
{
    public function __call($name, $arguments)
    {
        return call_user_func_array($name, $arguments);
    }
}
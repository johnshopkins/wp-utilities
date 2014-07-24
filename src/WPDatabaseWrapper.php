<?php

namespace WPUtilities;

class WPDatabaseWrapper
{
    public function run($query)
    {
        global $wpdb;
        return $wpdb->get_results($query);
    }
}

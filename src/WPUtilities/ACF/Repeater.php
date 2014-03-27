<?php

namespace WPUtilities\ACF;

class Repeater
{
    public function cleanMeta($meta)
    {
        // potential ACF fields based on integer value
        $potentials = $this->findIntegerValues($meta);

        // narrow the potentials to actuals

        foreach ($potentials as $potential) {

            // for repeater-like subfield meta keys (like "field_0_link")
            // also test keys like field_0_sub_field
            $regex = "/^" . $potential . "_(\d+)_(.+)$/";

            // loop through all meta fields and look for repeater-like keys
            foreach ($meta as $key => $value) {

                // if this is a repeater field
                if (preg_match($regex, $key, $matches)) {

                    $index = $matches[1];
                    $subfield = $matches[2];

                    if (!is_array($meta[$potential])) {
                        $meta[$potential] = array();
                    }

                    if (!isset($meta[$potential][$index]) || !is_array($meta[$potential][$index])) {
                        $meta[$potential][$index] = array();
                    }

                    $meta[$potential][$index][$subfield] = $value;
                    unset($meta[$key]);

                }
            }
        }

        return $meta;
    }

    protected function findIntegerValues($meta)
    {
        $ints = array();

        foreach ($meta as $k => $v) {

            if (is_numeric($v)) {
                $ints[] = $k;
            }
        }

        return $ints;
    }
}
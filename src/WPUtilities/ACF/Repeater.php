<?php

namespace WPUtilities\ACF;

class Repeater
{
    /**
     * Take metadata from a WordPress post and clean up
     * the ACF repeater fields
     * @param  array $meta Metadata
     * @return array Cleaned up metadata
     */
    public function cleanMeta($meta)
    {
        // potential ACF fields based on integer value
        $potentials = $this->findIntegerValues($meta);

        // narrow the potentials to actuals

        foreach ($potentials as $potential) {

            // for repeater-like subfield meta keys (like "field_0_link")
            // also test keys like field_0_sub_field
            $regex = "/^" . $potential . "_(\d+)_(.+)$/";
            $verifiedRepeater = false;

            // loop through all meta fields and look for repeater-like keys
            foreach ($meta as $key => $value) {

                // if this is a repeater field
                if (preg_match($regex, $key, $matches)) {
                    $verifiedRepeater = true;

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

            // only squash if this potential was a verified repeater (do not run on fields
            // that looked like repeaters (had integers)). These could potentially be
            // repeater subfields that have since been unset.
            if ($verifiedRepeater) {
                $meta[$potential] = $this->squashSimpleRepeater($meta[$potential]);
            }
            
        }

        return $meta;
    }

    /**
     * Some repeaters only have one subfield. For example, a field "IDs" has
     * subfields called "ID." In this case we don't need to keep a record of
     * the name of the subfield, so we just pass the ids back up to the "IDs"
     * field.
     * @param  array $repeater Repeater data
     * @return array
     */
    protected function squashSimpleRepeater($repeater)
    {
        if (!is_array($repeater) || count($repeater[0]) !== 1) {
            return $repeater;
        }

        return array_map(function ($a) {
            $keys = array_keys($a);
            $key = array_shift($keys);
            return $a[$key];
        }, $repeater);
    }

    public function createRepeater($array = array())
    {
        // $example = array(
        //     "images" => array(
        //         array(
        //             "type" => "low_resolution",
        //             "utl" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_6.jpg"
        //         ),
        //         array(
        //             "type" => "thumbnail",
        //             "utl" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_5.jpg"
        //         ),
        //         array(
        //             "type" => "standard_resolution",
        //             "utl" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_8.jpg"
        //         )
        //     )
        // );

        $meta = array();

        foreach ($array as $fieldName => $subfieldData) {

            $num = 0;

            foreach ($subfieldData as $subfields) {

                $meta[$fieldName] = count($subfieldData);

                foreach ($subfields as $k => $v) {
                    $meta["{$fieldName}_{$num}_{$k}"] = $v;
                }

                $num++;
                
            }

        }

        return $meta;

    }

    /**
     * Find meta keys whose values are integers
     * @param  array $meta Metadata
     * @return array Keys
     */
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
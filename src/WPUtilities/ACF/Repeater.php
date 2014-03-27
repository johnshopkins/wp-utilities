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
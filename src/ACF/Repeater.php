<?php

namespace WPUtilities\ACF;

class Repeater
{
    public function __construct()
    {

    }

    /**
     * Convery an array of data into metadata that can be
     * used to populate an ACF repeater field. Handy when
     * programatically importing data into WordPress.
     * @param  array  $array Data:
     * array(
     *    // repeater field name
     *    "images" => array(
     *        // data entry
     *        array(
     *            // subfields
     *            "type" => "low_resolution",
     *            "url" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_6.jpg"
     *        ),
     *        // data entry
     *        array(
     *            // subfields
     *            "type" => "thumbnail",
     *            "url" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_5.jpg"
     *        ),
     *        // data entry
     *        array(
     *            // subfields
     *            "type" => "standard_resolution",
     *            "url" => "http://distilleryimage4.s3.amazonaws.com/df3860c2b3bd11e3bd391271df4caaa4_8.jpg"
     *        )
     *    )
     * );
     * 
     * 
     * @return array  Formatted metada
     */
    public function createRepeater($array = array())
    {
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
}

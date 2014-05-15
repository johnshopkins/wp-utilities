<?php

namespace WPUtilities\ACF;

class Repeater
{
    protected $contentTypes;

    protected $wordpress;
    protected $wpquery_wrapper;

    protected $savedTypes = array();
    protected $repeaters = array();

    public function __construct($deps = array())
    {
        $this->contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : array();
        $this->wordpress = isset($deps["wordpress"]) ? $deps["wordpress"] : new \WPUtilities\WordPressWrapper();
        $this->wpquery_wrapper = isset($deps["wordpress_query"]) ? $deps["wordpress_query"] : new \WPUtilities\WPQueryWrapper();
    }

    /**
     * Take metadata from a WordPress post and clean up
     * the ACF repeater fields
     * @param  array  $meta     Metadata
     * @param  string $postType The type of post this meta belongs to
     * @return array Cleaned up metadata
     */
    public function cleanMeta($meta, $postType)
    {
        // find out which fields are repeaters on this post type
        if (!isset($this->repeaters[$postType])) {
            $fields = $this->contentTypes[$postType];
            foreach ($fields as $field) {
                if ($field["type"] != "repeater") {
                    continue;
                }
                $this->repeaters[$postType][$field["name"]] = $field["sub_fields"];
            }
        }

        if (!isset($this->repeaters[$postType])) {
            return $meta;
        }

        return $this->compileRepeaters($meta, $this->repeaters[$postType]);
    }

    protected function compileRepeaters($meta, $repeaterFields)
    {
        foreach ($repeaterFields as $name => $subfields) {

            $subfields = array_map(function ($subfield) {
                return $subfield["name"];
            }, $subfields);

            $meta[$name] = array();

            foreach ($meta as $key => $value) {

                $regex = "/^" . $name . "_(\d+)_(.+)$/";

                if (preg_match($regex, $key, $matches)) {

                    $index = $matches[1];
                    $subfield = $matches[2];

                    if (!in_array($subfield, $subfields)) {
                        // repeater imposter
                        continue;
                    }

                    if (!isset($meta[$name][$index]) || !is_array($meta[$name][$index])) {
                        $meta[$name][$index] = array();
                    }

                    if (count($subfields) > 1) {
                        $meta[$name][$index][$subfield] = $value;
                    } else {
                        $meta[$name][$index] = $value;
                    }
                    
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
}

<?php

namespace WPUtilities\ACF;

class Repeater
{
    protected $wordpress;
    protected $wpquery_wrapper;
    protected $contentTypes;

    protected $savedTypes = array();
    protected $repeaters = array();

    public function __construct()
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);

        $this->wordpress = isset($args["wordpress"]) ? $args["wordpress"] : new \WPUtilities\WordPressWrapper();
        $this->wpquery_wrapper = isset($args["wordpress_query"]) ? $args["wordpress_query"] : new \WPUtilities\WPQueryWrapper();
        $this->contentTypes = isset($args["acf_contentTypes"]) ? $args["acf_contentTypes"] : new ContentTypes();
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
        // get the content type information, once per load
        if (!$this->savedTypes) {
            $this->savedTypes = $this->contentTypes->find();
        }

        // find out which fields are repeaters on this post type
        if (!isset($this->repeaters[$postType])) {
            $fields = $this->savedTypes[$postType];
            foreach ($fields as $field) {
                if ($field["type"] != "repeater") {
                    continue;
                }
                $this->repeaters[$postType][$field["name"]] = $field["sub_fields"];
            }
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

                    if (!is_array($meta[$name])) {
                        $meta[$name] = array();
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

    /**
     * Find meta keys whose values are integers
     * @param  array $meta Metadata
     * @param  boolean $zero Return only keys whose value is 0
     * @return array Keys
     */
    // protected function findIntegerValues($meta, $zero = false)
    // {
    //     $ints = array();

    //     foreach ($meta as $k => $v) {

    //         $shouldEqual = $zero ? 0 : $v;

    //         if (is_numeric($v) && (int) $v == $shouldEqual) {
    //             $ints[] = $k;
    //         }
            
    //     }

    //     return $ints;
    // }
}

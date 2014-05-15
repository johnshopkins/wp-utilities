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
     * the ACF repeater fields.
     * 
     * When a post is sent through this method, the repeater fields
     * on that post type are found and saved to $this->repeaters
     * so that if another post of the same type is sent through
     * this function later, the process of finding which fields are
     * repeaters does not have to be repeated.
     *
     * This method should not be called directly. Please instantiate
     * an instance of WPUtilities\ACF\ContentTypes and run 
     * ContentTypes::cleanMeta().
     * 
     * @param  array  $meta     Metadata
     * @param  string $postType The type of post this meta belongs to
     * @return array Cleaned up metadata
     */
    public function cleanMeta($meta, $postType)
    {
        if (!isset($this->repeaters[$postType])) {

            // This is the first time a post of this type has been run through this
            // function. Find out which fields on this post type are repeater fields.

            $postTypeFields = $this->contentTypes[$postType];
            $this->repeaters[$postType] = array();

            foreach ($postTypeFields as $field) {
                if ($field["type"] != "repeater") continue;
                $this->repeaters[$postType][$field["name"]] = $field["sub_fields"];
            }

        }

        if (empty($this->repeaters[$postType])) {

            // this post type does not have any repeater fields

            return $meta;
        }

        // compile the repeaters that we found
        return $this->compileRepeaters($meta, $this->repeaters[$postType]);
    }

    /**
     * Loop through the array of repeater fields on this post type, then
     * loop through each meta data looking for keys that match the regex.
     * They will look something like fieldName_0_subFieldName. If we find
     * a match, we assign it to the metadata in a more readable way.
     *
     * fieldName_0_subFieldName => "some value"
     * fieldName_0_anotherSubFieldName => "another value"
     * 
     * becomes...
     *
     * fieldName => array(
     *     "subFieldName" => "some value",
     *     "anotherSubFieldName" => "another value"
     * );
     *
     * 
     * @param  array $meta            Metadata from WordPress
     * @param  array $repeaterFields  Array of fields that are repeaters
     * @return array Metadata with nicely formatted repeater fields
     */
    protected function compileRepeaters($meta, $repeaterFields)
    {
        foreach ($repeaterFields as $name => $subfields) {

            // extract just the names of the subfields of this
            // repeater field so we can do an in_array() search later
            $subfields = array_map(function ($subfield) {
                return $subfield["name"];
            }, $subfields);

            // Initiate an array to store this repeater data in on
            // the meta array. If there is no data in this repeater
            // field, it will be reflected as an empty array
            $meta[$name] = array();

            // Metadata pattern we're looking for. Equates to
            // something like /^fieldName_(\d+)_(subFieldName|anotherSubFIeldName)$/
            $regex = "/^" . $name . "_(\d+)_(" . implode("|", $subfields) .")$/";

            foreach ($meta as $key => $value) {

                if (preg_match($regex, $key, $matches)) {

                    // this metadata key matches the repeater pattern!

                    $index = $matches[1];       // ordered location
                    $subfield = $matches[2];    // subfield name

                    // if ex: $meta["fieldName"][0] doesn't exist or isn't an array...
                    // When isn't this an array?
                    if (!isset($meta[$name][$index]) || !is_array($meta[$name][$index])) {
                        $meta[$name][$index] = array();
                    }

                    if (count($subfields) > 1) {

                        // there more than one subfield in this array, so nest
                        // the data in the appropiate subfield

                        $meta[$name][$index][$subfield] = $value;
                    } else {

                        // there is only one subfield in this array, so don't
                        // nest the data in an array

                        $meta[$name][$index] = $value;
                    }
                    
                    // unset the crazy ACF meta key
                    unset($meta[$key]);

                }
            }

        }

        return $meta;
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

<?php

namespace WPUtilities\ACF;

class Supertags
{
    protected $contentTypes = array();
    protected $supertags = array();
    protected $relationships = array();

    public function __construct($deps = array())
    {
        $this->contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : array();
        $this->compile();

    }

    public function find()
    {
        return $this->supertags;
    }

    public function findRelationships()
    {
        return $this->relationships;
    }

    /**
     * Loop through all the content types compiled by WPUtilities\ACF\ContentTypes
     * and figure out which fields are supertag fields.
     *
     * Compiles an array assigned to $this->supertags
     *
     * array(
     *     {post_type} => array(
     *          array(
     *              name: "field_name",
     *              multiple: 0,
     *
     *              parent: "parent_field_name", // if this supertags is contained within a repeater field
     *              onlyChild: true // if this supertags is contained within a repeater field AND its the only subfield of the repeater field (helps with meta formatting)
     *          )
     *     )
     * )
     * 
     * @return null
     */
    public function compile()
    {  
        foreach($this->contentTypes as $type => $fields) {

            $this->relationships[$type] = array();
            $this->supertags[$type] = array();

            foreach ($fields as $field) {

                if ($field["type"] == "repeater") {

                    foreach ($field["sub_fields"] as $subfield) {
                        $this->analyzeField($type, $subfield, $field["name"], count($field["sub_fields"]) == 1);
                    }
                    continue;

                }

                $this->analyzeField($type, $field);
            }
        }
    }

    /**
     * Analyze a field for its supertaginess and assign it to
     * $this->supertags if it is a supertag field.
     * @param  string  $type      Post type
     * @param  array   $field     Field being analyzed
     * @param  array   $parent    Name of parent field, if applicable
     * @param  boolean $onlyChild Only subfield of parent?
     * @return null
     */
    protected function analyzeField($type, $field, $parent = null, $onlyChild = false)
    {
        if ($field["type"] != "supertags") {
            return;
        }

        foreach ($field["vocabs"] as $vocab) {

            $this->relationships[$type][$vocab][] = array(
                "name" => $field["name"],
                "multiple" => $field["multiple"],
                "parent" => $parent,
                "onlyChild" => $onlyChild
            );

            if ($parent) {
                $this->supertags[$type][$parent]["children"][$field["name"]] = $field;
            } else {
                $this->supertags[$type][$field["name"]] = $field;
            }
            
        }
    }

    /**
     * Cleans metadata of supertag fields that only allow one
     * value. All supertag data is stored as an array, even
     * if only one value is allowed. This function finds those
     * needless arrays and pops the data out.
     * @param  array  $meta Metadata
     * @param  string $type Post type
     * @return array Cleaned meta
     */
    public function cleanMeta($meta, $type)
    {
        // print_r($meta); die();
        // supertags on this content type
        $supertags = $this->supertags[$type];

        // print_r($meta);
        // print_r($supertags); die();

        foreach ($meta as $k => $v) {

            if (!in_array($k, array_keys($supertags))) {
                // not a supertag field
                continue;
            }

            $supertagFieldDetails = $supertags[$k];            

            if (isset($supertagFieldDetails["children"])) {

                // this is a repeater field that has supertag
                // field(s) on it

                $children = $supertagFieldDetails["children"];

                if (count($children) == 1) {

                    $child = array_shift($children);

                    if ($child["multiple"]) continue;
                    
                    $meta[$k] = array_map(function ($value) {
                        return array_shift($value);
                    }, $meta[$k]);

                } else {

                    // $v = array(
                    //     "subFieldName" => array(
                    //         1234
                    //     )
                    // )

                    foreach ($v as $index => $childField) {

                        foreach ($childField as $subFieldName => $value) {
                            if ($children[$subFieldName]["multiple"]) continue;
                            $meta[$k][$index][$subFieldName] = array_shift($value);
                        }


                    }
                }

            } else {
                if ($supertagFieldDetails["multiple"]) continue;
                $meta[$k] = array_shift($meta[$k]);
            }

        }

        return $meta;
    }

}

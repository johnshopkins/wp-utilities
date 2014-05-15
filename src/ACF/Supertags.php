<?php

namespace WPUtilities\ACF;

class Supertags
{
    protected $contentTypes = array();
    protected $supertags = array();

    public function __construct($deps = array())
    {
        $this->contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : array();
        $this->compile();
    }

    public function find()
    {
        return $this->supertags;
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

            $this->supertags[$type][$vocab][] = array(
                "name" => $field["name"],
                "multiple" => $field["multiple"],
                "parent" => $parent,
                "onlyChild" => $onlyChild
            );
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
        foreach ($this->supertags[$type] as $contentType) {

            foreach ($contentType as $supertagField) {

                // if this field allows multiple values, it's good to go
                if ($supertagField["multiple"]) continue;

                $supertagName = $supertagField["name"];
                $parent = $supertagField["parent"];


                if (!$parent) {
                    // top-level supertag
                    $meta[$supertagName] = array_shift($meta[$supertagName]);
                    continue;
                }


                // supertag as a subfield of a repeater
                $parent = $supertagField["parent"];

                if ($supertagField["onlyChild"]) {

                    // there is only one subfield to this repeater:
                    // array(
                    //  array(1234),    // subfield
                    //  array(2345)     // subfield
                    // )

                    $meta[$parent] = array_map(function ($v) {
                        return array_shift($v);
                    }, $meta[$parent]);

                } else {

                    // there are multiple subfields to this repeater:
                    
                    // array(
                    //  array(
                    //      "someField" => array(1234),
                    //      "anotherField" => array(1234)
                    //  ),
                    //  array(
                    //      "someField" => array(5678),
                    //      "anotherField" => array(5678)
                    //  )
                    // )

                    // print_r($meta[$parent]);


                    // this is flawed
                    // 
                    $meta[$parent] = array_map(function ($v) use ($supertagName) {
                        
                        foreach ($v as $type => $data) {

                            if ($type == $supertagName) {
                                $v[$supertagName] = array_shift($v[$supertagName]);
                            }

                        }

                        return $v;

                    }, $meta[$parent]);

                }

            }

        }

        return $meta;
    }

}

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

}

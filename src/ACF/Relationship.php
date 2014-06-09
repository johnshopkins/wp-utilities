<?php

namespace WPUtilities\ACF;

class Relationship
{
    protected $contentTypes = array();
    protected $relationships = array();

    public function __construct($deps = array())
    {
        $this->contentTypes = isset($deps["contentTypes"]) ? $deps["contentTypes"] : array();
        $this->compile();

    }

    public function find()
    {
        return $this->relationships;
    }

    /**
     * Loop through all the content types compiled by WPUtilities\ACF\ContentTypes
     * and figure out which fields are relationship fields.
     * @return null
     */
    public function compile()
    {  
        foreach($this->contentTypes as $type => $fields) {

            // we don't want to relate stuff to related content areas
            if ($type == "related_content") continue;

            $this->relationships[$type] = array();

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
     * Analyze a field for its relationship status.
     * @param  string  $type      Post type
     * @param  array   $field     Field being analyzed
     * @param  array   $parent    Name of parent field, if applicable
     * @param  boolean $onlyChild Only subfield of parent?
     * @return null
     */
    protected function analyzeField($type, $field, $parent = null, $onlyChild = false)
    {
        if ($field["type"] != "relationship") {
            return;
        }

        foreach ($field["post_type"] as $vocab) {

            $this->relationships[$type][$vocab][] = array(
                "name" => $field["name"],
                "multiple" => $field["max"] !== 1,
                "parent" => $parent,
                "onlyChild" => $onlyChild
            );
            
        }
    }

}

<?php

namespace WPUtilities\ACF;

class Supertags
{
    protected $contentTypes;

    public function __construct($contentTypes = array())
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);

        $this->contentTypes = $contentTypes;
    }

    public function find()
    {  
        $supertags = array();

        foreach($this->contentTypes as $type => $fields) {

            $supertags[$type] = array();

            foreach ($fields as $i => $field) {

                if ($field["type"] != "supertags") {
                    continue;
                }

                foreach ($field["vocabs"] as $vocab) {

                    $supertags[$type][$vocab][] = array(
                        "name" => $field["name"],
                        "multiple" => $field["multiple"]
                    );
                }
            }
        }

        return $supertags;
    }

}

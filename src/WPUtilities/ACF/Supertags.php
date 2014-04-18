<?php

namespace WPUtilities\ACF;

class Supertags
{
    protected $contentTypes;

    public function __construct()
    {
        // allow for dependency injection (testing)
        $args = func_get_args();
        $args = array_shift($args);

        $this->contentTypes = isset($args["acf_contentTypes"]) ? $args["acf_contentTypes"] : new ContentTypes();
    }

    public function find()
    {  
        $contentTypes = $this->contentTypes->find();
        return $this->findSupertags($contentTypes);
    }

    protected function findSupertags($contentTypes)
    {
        $supertags = array();

        foreach($contentTypes as $type => $fields) {

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
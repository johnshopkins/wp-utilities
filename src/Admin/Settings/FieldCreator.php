<?php

namespace WPUtilities\Admin\Settings;
 
class FieldCreator
{
    public static function checkbox_group($name, $args)
    {
        if (!empty($args["html"])) return $args["html"];

        $html = "";

        foreach ($args["options"] as $k => $v) {

            $fieldName = "{$name}[$k]";

            $checked = isset($args["value"][$k]) && $args["value"][$k] == 1 ? "checked=checked" : "";

            $html .= "<input type='checkbox' name='{$fieldName}' value='1' {$checked} />";

            if (!empty($v)) {
                $html .= " <label for='{$fieldName}'>{$v}</label>";
            }

            $html .= "<br />";

        }

        if (isset($args["description"])) {
            $html .= "<p class='description'>{$args['description']}</p>";
        }

        return $html;
    }

    public static function select($name, $args)
    {
        if (!empty($args["html"])) return $args["html"];

        $html = "<select name='{$name}' id='{$name}'>";
        $html .= "<option value=''>— Select —</option>";

        foreach ($args["options"] as $k => $v) {
            $selected = $args["value"] == $k ? "selected=selected" : "";
            $html .= "<option value='{$k}' {$selected}>{$v}</option>";
        }

        $html .= "</select>";

        if (isset($args["description"])) {
            $html .= "<p class='description'>{$args['description']}</p>";
        }

        return $html;
    }

    public static function text($name, $args)
    {
        if (!empty($args["html"])) return $args["html"];
        
        $html = "<input type='text' name='{$name}' value='{$args['value']}' class='regular-text' />";

        if (isset($args["description"])) {
            $html .= "<p class='description'>{$args['description']}</p>";
        }
        
        return $html;
    }
}

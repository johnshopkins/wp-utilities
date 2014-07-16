<?php

namespace WPUtilities\Admin\Settings;
 
class FieldCreator
{
    public static function checkbox_group($name, $args)
    {
        $html = "";

        foreach ($args["options"] as $k => $v) {

            $name = "{$name}[$k}";

            $checked = isset($args["value"][$k]) && $args["value"][$k] == 1 ? "checked=checked" : "";

            $html .= "<input type='checkbox' name='{$name}' value='1' {$checked} />";

            if (!empty($v)) {
                $html .= " <label for='{$name}'>{$v}</label>";
            }

            $html .= "<br />";

        }

        return $html;
    }

    public static function select($name, $args)
    {
        $html = "<select name='{$name}' id='{$name}'>";
        $html .= "<option value=''>— Select —</option>";

        foreach ($args["options"] as $k => $v) {
            $selected = $args["value"] == $k ? "selected=selected" : "";
            $html .= "<option value='{$k}' {$selected}>{$v}</option>";
        }

        $html .= "</select>";

        return $html;
    }

    public static function text($name, $args)
    {
        return "<input type='text' name='{$name}' value='{$args['value']}' class='regular-text' />";
    }
}

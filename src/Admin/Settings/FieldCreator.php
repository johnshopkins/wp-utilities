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

    public static function text($name, $args)
    {
        $html = "";

        // if ($args["label"]) {
        //     $html .= "<label for='{$name}'>{$args['label']}</label> ";
        // }

        $html .= "<input type='text' name='{$name}' value='{$args['value']}' class='regular-text' />";

        return $html;
    }

    public static function select($name, $label, $value)
    {
        $html = "";

        if ($label) {
            $html .= "<label for=\"{$name}\">{$label}</label> ";
        }

        $html .= "<input type=\"text\" name=\"{$name}\" value=\"" . $value . "\" class=\"regular-text\">";

        return $html;
    }
}

<?php

namespace WPUtilities\Admin\Settings;
 
class FieldCreator
{
    public static function checkbox($name, $label, $value, $checked = "")
    {
        $html = "<input type=\"checkbox\" id=\"\" name=\"{$name}\" value=\"1\" " . $checked . " />";

        if ($label) {
            $html .= " <label for=\"{$name}\">{$label}</label><br />";
        }

        return $html;
    }

    public static function text($name, $label, $value)
    {
        $html = "";

        if ($label) {
            $html .= "<label for=\"{$name}\">{$label}</label> ";
        }

        $html .= "<input type=\"text\" id=\"\" name=\"{$name}\" value=\"" . $value . "\" class=\"regular-text\">";

        return $html;
    }
}
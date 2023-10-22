<?php

class InjectStyles
{
    private static array $style_list = [];

    /**
     * Inject new stylesheets
     * @param array [name => "name of task", style => "style"]
     */
    public static function set_style($data): void
    {
        static::$style_list[$data['name']] = [
            "style" => $data["style"],
        ];

    }

    public static function inject_style(): string
    {

        $output = "";
        foreach (static::$style_list as $scripts) {
            $output .= '<link rel="stylesheet" href="' . $scripts['style'] . '">';
        }

        return $output;


    }



}
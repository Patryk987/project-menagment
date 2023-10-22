<?php

class InjectStyles
{
    private static array $style_list = [];
    private static int $num = 0;

    /**
     * Inject new stylesheets
     * @param array [name => "name of task", style => "style"]
     */
    public static function set_style($data): void
    {
        static::$style_list[$data['name']] = [
            "style" => $data["style"],
        ];

        echo "SetStyle";
        echo static::$num;

        static::$num++;
    }

    public static function inject_style(): string
    {
        echo "ShowStyle";
        echo static::$num;

        static::$num++;
        // var_dump(static::$style_list);
        var_dump(static::$style_list);
        $output = "";
        foreach (static::$style_list as $scripts) {
            $output .= '<link rel="stylesheet" href="' . $scripts['style'] . '">';
        }

        return $output;


    }



}
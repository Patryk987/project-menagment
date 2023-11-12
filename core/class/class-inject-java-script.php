<?php

class InjectJavaScript
{
    private static array $function_list = [];

    /**
     * Inject new js script
     * @param array [name => "name of task", script => "script"]
     */
    public static function set_script($data): void
    {
        static::$function_list[$data['name']] = [
            "script" => $data["script"],
        ];

    }

    public static function inject_script(): string
    {

        $output = "";
        foreach (static::$function_list as $scripts) {
            $output .= "<script  nonce='" . \NONCE . "' src='" . $scripts['script'] . "'></script>\n\t";
        }

        return $output;


    }



}
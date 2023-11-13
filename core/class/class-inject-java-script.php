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
            "type" => !empty($data["type"]) ? $data["type"] : 'link',
            "src" => !empty($data["src"]) ? $data["src"] : '',
            "script" => !empty($data["script"]) ? $data["script"] : '',
        ];

    }

    public static function inject_script(): string
    {

        $output = "";
        foreach (static::$function_list as $scripts) {
            if ($scripts["type"] == "link")
                $output .= "<script  nonce='" . \NONCE . "' src='" . $scripts['src'] . "'></script>\n\t";
            else
                $output .= "<script  nonce='" . \NONCE . "'>" . $scripts['script'] . "</script>\n\t";
        }

        return $output;


    }



}
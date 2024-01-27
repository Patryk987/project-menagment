<?php

namespace ModuleManager;

class Translate
{
    public static array $file;
    public string $lange;
    public function __construct($lange = "en")
    {
        $this->lange = $lange;
        if (empty(static::$file))
            $this->get_translate_text();
    }


    public function get_text($key)
    {

        if (!empty(static::$file[$key]) && !empty(static::$file[$key][$this->lange])) {
            return static::$file[$key][$this->lange];
        } else {
            return '{' . $key . '}';
        }

    }

    // private 

    private function get_translate_text()
    {
        static::$file = json_decode(file_get_contents(__DIR__ . "/../../config/translate.json"), true);
    }
}
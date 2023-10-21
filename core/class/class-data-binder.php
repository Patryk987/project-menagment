<?php

namespace ModuleManager;

class DataBinder
{
    static array $binders = [];

    public function __construct()
    {

    }

    /**
     * 
     */
    public static function set_binder($data): void
    {
        static::$binders[$data['key']] = [
            "function" => $data['function']
        ];

    }

    /**
     * 
     */
    public static function get_binder($key)
    {
        if (!empty(static::$binders[$key]['function'])) {

            return static::$binders[$key]['function'];

        } else {

            return null;

        }

    }
}
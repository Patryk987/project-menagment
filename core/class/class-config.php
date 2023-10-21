<?php

namespace ModuleManager;

class Config
{
    public static function get_config(): array
    {

        $config = file_get_contents("./config/config.json");
        $config = json_decode($config);

        return (array) $config;

    }
}
<?php

class useDatabase
{
    public function __construct()
    {

        ModuleManager\Pages::set_endpoint([
            "link" => 'database_structure',
            "function" => [$this, 'get_database_structure'],
            "http_methods" => 'GET',
            "permission" => [0]
        ]);

        ModuleManager\Pages::set_endpoint([
            "link" => 'database_structure',
            "function" => [$this, 'save_database_structure'],
            "http_methods" => 'POST',
            "permission" => [0]
        ]);

    }

    public function get_database_structure($input): void
    {

        $main = new ModuleManager\Main;
        foreach ($main->sedjm->generate_database() as $value) {
            echo $value . "\n";
        }

    }
    public function save_database_structure($input): array
    {

        $main = new ModuleManager\Main;
        if ($main->sedjm->generate_database(true)) {
            $output['status'] = true;
        } else {
            $output['status'] = false;
        }

        return $output;
    }

}

new useDatabase;
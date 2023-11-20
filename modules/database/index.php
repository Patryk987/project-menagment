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

        global $main;
        foreach ($main->sedjm->generate_database() as $value) {
            echo $value . "\n";
        }

    }
    public function save_database_structure($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);

        $main = new ModuleManager\Main;
        if ($main->sedjm->generate_database(true)) {
            // $output['status'] = true;
            $output->set_status(\ApiStatus::CORRECT);
        } else {
            // $output['status'] = false;
            $output->set_status(\ApiStatus::ERROR);
        }

        return $output;
    }

}

new useDatabase;
<?php

namespace ModuleManager;

/* 
 * Połączenie z bazą danych 
 */
trait DatabaseConnect
{
    protected $connect;

    private function get_database_structure(string $db_name)
    {

        $database = file_get_contents('./config/database.json');
        $database = html_entity_decode($database);
        $database = json_decode($database, true);

        return $database[$db_name];

    }

    private function get_database_connect(string $db_name)
    {

        $database_config = file_get_contents('./config/database_config.json');
        $database_config = html_entity_decode($database_config);
        $database_config = json_decode($database_config, true);

        return $database_config[$db_name];

    }

    protected function get_db_connect(string $db_name)
    {
        $database = $this->get_database_structure($db_name);
        $database_config = $this->get_database_connect($db_name);

        $this->connect = new \mysqli(
            $database_config['db_host'],
            $database_config['db_user'],
            $database_config['db_password'],
            $database_config['db_database']
        );

        if (!mysqli_set_charset($this->connect, "utf8")) {

            Main::set_error("Wystąpił problem z kodowaniem UTF-8", 'ERROR');
            // exit();
        }

        if ($this->connect->connect_errno) {

            $error = "Failed to connect to MySQL: " . $this->connect->connect_error;
            Main::set_error($error, 'ERROR');
            // exit();
        }

        return $database;

    }

    public function __destruct()
    {
        $this->connect->close();
    }

}
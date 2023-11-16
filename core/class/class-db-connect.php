<?php

namespace ModuleManager;

/* 
 * Połączenie z bazą danych 
 */
class DatabaseConnect
{
    public $connect;

    static public array $database_structure = [];

    public function __construct()
    {
        $this->map_database_extends();
    }

    static public function set_database_fragment(array $fragment): void
    {
        self::$database_structure = array_merge(self::$database_structure, $fragment);
    }

    private function map_database_extends()
    {
        try {
            $dir_path = './modules/';
            $modules = scandir($dir_path);


            foreach ($modules as $key => $value) {

                if ($value != ".." && $value != ".") {
                    $file_in_module = scandir($dir_path . $value);
                    if (in_array("extends-database.php", $file_in_module)) {
                        try {
                            include_once $dir_path . $value . '/extends-database.php';
                        } catch (\Throwable $th) {
                            $details = [
                                "message" => $th->getMessage(),
                                "code" => $th->getCode(),
                                "file" => $th->getFile(),
                                "line" => $th->getLine()
                            ];
                            \ModuleManager\Main::set_error('Include module', 'ERROR', $details);
                        }

                    }
                }

            }
        } catch (\Throwable $th) {
            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            \ModuleManager\Main::set_error('Include module', 'ERROR', $details);
        }
    }

    private function get_database_structure(string $db_name)
    {

        // $database = file_get_contents('./config/database.json');
        // $database = html_entity_decode($database);
        // $database = json_decode($database, true);
        // return $database[$db_name];

        return self::$database_structure;

    }

    private function get_database_connect(string $db_name)
    {

        $database_config = file_get_contents('./config/database_config.json');
        $database_config = html_entity_decode($database_config);
        $database_config = json_decode($database_config, true);

        return $database_config[$db_name];

    }

    public function get_db_connect(string $db_name)
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
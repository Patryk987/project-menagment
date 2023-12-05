<?php

namespace ModuleManager;

define("DB_NAME", "cms");
define("NONCE", uniqid());

// Models
require_once("./core/models/model-api.php");
require_once("./core/models/model-projects.php");


// Interface
require_once("./core/interface/interface-repository.php");

// Enums
require_once("./core/enums/enums-collaborators.php");
require_once("./core/enums/enums-project-status.php");
require_once("./core/enums/enums-api-status.php");

// Repository
require_once("./core/repository/repository-projects.php");

require_once("core/class/class-projects.php");

require_once("./core/class/class-read-env.php");

require_once("./core/class/class-data-binder.php");
require_once('./core/class/class-encrypt.php');

require_once("./core/class/class-helper.php");
require_once("./core/class/load-file.php");

require_once("./core/class/class-file-upload.php");
require_once("./core/class/class-config.php");
require_once("./core/class/trait-debug.php");
require_once("./core/class/class-send-mail.php");
require_once("./core/class/class-db-connect.php");
require_once('./core/class/trait-data-validation.php');
require_once('./core/class/trait-jwt.php');
require_once("./core/class/class-sedjm.php");
require_once("./core/class/class-account.php");
require_once("./core/class/trait-api.php");
require_once("./core/class/trait-module.php");
require_once("./core/class/class-pages.php");
require_once("./core/class/class-local-storage.php");
require_once("./core/class/class-table.php");
require_once("./core/class/async-select.php");
require_once("./core/class/notification.php");
require_once("./core/class/class-get-csv.php");
require_once("./core/class/logger.php");

require_once("./core/class/class-inject-java-script.php");
require_once("./core/class/class-inject-style.php");

require_once("./core/function/function.php");

require_once("./core/class/class-swaf.php");


class Main
{
    use Debug;

    public array $config;
    public static array $sub_pages;
    public SEDJM $sedjm;
    protected array $database;
    public Pages $pages;
    public Accounts $accounts;
    public Popups $popups;
    public string $page_name;
    public static $token;
    public static $jwt;

    public DatabaseConnect $db_connect;

    public function __construct()
    {

        if (empty($_SESSION['tmp_key'])) {
            $_SESSION['tmp_key'] = uniqid();
        }

        $database_core_table = file_get_contents('./config/database.json');
        $database_core_table = html_entity_decode($database_core_table);
        $database_core_table = json_decode($database_core_table, true);
        DatabaseConnect::set_database_fragment($database_core_table[DB_NAME]);


        // Error Trigger 
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);

        $this->config = Config::get_config();
        $debug = $this->config['debug'];

        $this->start_debug($debug);

        // Anti-clickjacking
        if ($this->config['Anti-clickjacking'])
            header('X-Frame-Options: SAMEORIGIN');
        else
            header('X-Frame-Options: DENY');

        static::$sub_pages = $this->get_sub_page();


    }


    private function get_sub_page(): array
    {
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $base_link = $link . "/";
        $actual_link = $link . $_SERVER["REQUEST_URI"];

        $sub_pages = str_replace($base_link, "", $actual_link);

        while (str_contains($sub_pages, "//")) {
            $sub_pages = str_replace("//", "/", $sub_pages);
        }

        $sub_pages = explode("?", $sub_pages)[0];
        $sub_pages = explode("/", $sub_pages);

        if (empty(end($sub_pages))) {
            array_pop($sub_pages);
        }

        return $sub_pages;

    }

    // Set Env
    public function set_env()
    {
        $load_env = new LoadEnv(__DIR__ . "/../config/");
        $load_env->initEnv();
    }

    // Set tokens
    public function set_token()
    {

        // Set JWT
        static::$jwt = new JWT($_ENV['JWT_TOKEN']);


        $token = LocalStorage::get_data("token", 'session', true);
        if (empty($token)) {
            $token = LocalStorage::get_data("token", 'cookie', true);
            LocalStorage::set_data("token", $token, 'session', true);
        }

        static::$token = static::$jwt->check_token($token);

    }

    // connect to database
    public function set_database_connect()
    {

        $this->db_connect = new DatabaseConnect();
        $this->database = $this->db_connect->get_db_connect(DB_NAME);
        $this->sedjm = new SEDJM($this->db_connect->connect, $this->database);
        $this->accounts = new Accounts($this->sedjm);
    }

    // Pages
    public function set_pages()
    {

        $this->pages = new Pages(static::$token, $this->config, static::$sub_pages);
        $this->page_name = $this->pages->get_last_page();

        $this->popups = new Popups;

    }


    public function __destruct()
    {

        $debug_info = $this->end_debug();

        if (!empty($debug_info)) {

            $path = __DIR__ . "/logger/logs.log";
            $logger = new Logger($path);

            // $logger->log('Working time: ' . $debug_info['working_time'], 'INFO');
            // echo 'Memory usage: ' . round(memory_get_usage() / 1048576, 2) . "M\n";

            foreach ($debug_info['error_list'] as $key => $value) {
                $logger->log($value['name'] . ": " . implode(',', $value['details']), $value['type']);
            }

            $logger->close();
        }

    }
}
<?php

namespace ModuleManager;

define("DB_NAME", "cms");
define("NONCE", uniqid());

// Models
require_once("./core/models/model-projects.php");


// Interface
require_once("./core/interface/interface-repository.php");

// Enums
require_once("./core/enums/enums-collaborators.php");
require_once("./core/enums/enums-project-status.php");

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
require_once("./core/class/trait-db-connect.php");
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
    use Debug, DatabaseConnect, JWT;

    public array $config;
    public array $sub_pages;
    public SEDJM $sedjm;
    protected array $database;
    public Pages $pages;
    public Accounts $accounts;
    public Popups $popups;
    public string $page_name;
    public static $token;

    public function __construct()
    {

        // Set Env
        $load_env = new LoadEnv(__DIR__ . "/../config/");
        $load_env->initEnv();

        // Set config
        $this->config = Config::get_config();
        $debug = $this->config['debug'];

        $this->start_debug($debug);

        // header("Content-Security-Policy: default-src 'self'; script-src 'self'", FALSE);

        // Anti-clickjacking
        if ($this->config['Anti-clickjacking'])
            header('X-Frame-Options: SAMEORIGIN');
        else
            header('X-Frame-Options: DENY');

        // connect to database
        $this->database = $this->get_db_connect(DB_NAME);

        $this->sedjm = new SEDJM($this->connect, $this->database);

        $this->accounts = new Accounts($this->sedjm);

        $token = LocalStorage::get_data("token", 'session', true);
        if (empty($token)) {
            $token = LocalStorage::get_data("token", 'cookie', true);
            LocalStorage::set_data("token", $token, 'session', true);
        }

        // Error Trigger 
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler']);

        // Tokens

        static::$token = $this->check_token($token);

        // Pages
        $this->pages = new Pages(static::$token, $this->config);

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

            foreach ($debug_info['error_list'] as $key => $value) {
                $logger->log($value['name'] . ": " . implode(',', $value['details']), $value['type']);
            }

            $logger->close();

        }
    }
}
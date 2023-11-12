<?php

require_once "./core/index.php";
require_once "./template/default/functions.php";


$main = new ModuleManager\Main;
$main->set_env();
$main->set_token();
$main->set_database_connect();
$main->set_pages();
$main->pages->load_page();
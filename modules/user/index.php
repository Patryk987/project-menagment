<?php

namespace Users;

require_once __DIR__ . "/controller/user-controller.php";

$user_controller = new Controller\UsersController();
$user_controller->init_page();
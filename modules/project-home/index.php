<?php

namespace Home;

require_once __DIR__ . "/controller/home-controller.php";

$home_controller = new Controller\HomeProjectController();
$home_controller->init_page();
<?php

namespace Home;

require_once(__DIR__ . "/controller/home-controller.php");
require_once(__DIR__ . "/controller/dashboard-controller.php");

// Dashboard
$dashboard_controller = new DashboardController;
$dashboard_controller->set_dashboard_blocks();

new HomeController;
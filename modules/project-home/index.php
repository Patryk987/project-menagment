<?php

namespace HomeProject;

// controllers 
require_once __DIR__ . "/controller/home-controller.php";
require_once __DIR__ . "/controller/dashboard-controller.php";

$project_dashboard = new Controller\ProjectDashboardController();
$project_dashboard->set_dashboard_blocks();

$home_controller = new Controller\HomeProjectController();
$home_controller->init_page();
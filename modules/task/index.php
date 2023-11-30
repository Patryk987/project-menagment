<?php

namespace Tasks;

/**
 * Required modules: projects
 */

// Helper

// Model

// Enums
require_once __DIR__ . "/enums/enum-task-group-status.php";
require_once __DIR__ . "/enums/enum-task-status.php";

// repository
require_once __DIR__ . "/repository/repository-task-group.php";
require_once __DIR__ . "/repository/repository-task.php";
require_once __DIR__ . "/repository/repository-task-status.php";

// controller
require_once __DIR__ . "/controller/controller-tasks-page.php";
require_once __DIR__ . "/controller/controller-tasks-api.php";
require_once __DIR__ . "/controller/controller-tasks-group-api.php";
require_once __DIR__ . "/controller/controller-tasks-status-api.php";


// Init pages
new \Tasks\Controller\TasksPageController();

// Init api
new \Tasks\Controller\TasksStatusApiController();
new \Tasks\Controller\TasksApiController();
new \Tasks\Controller\TasksGroupApiController();


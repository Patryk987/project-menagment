<?php

namespace Tasks;

/**
 * Required modules: projects
 */

// Helper

// Model

// repository
require_once __DIR__ . "/repository/repository-task-group.php";
require_once __DIR__ . "/repository/repository-task.php";

// controller
require_once __DIR__ . "/controller/controller-tasks-page.php";


// Init pages
new \Tasks\Controller\TasksPageController();

// Init api


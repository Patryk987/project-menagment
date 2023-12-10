<?php

namespace Projects;

// Enums
require_once __DIR__ . "/enums/enum-collaborators-role.php";
require_once __DIR__ . "/enums/enum-collaborators-status.php";


// Model
require_once __DIR__ . "/model/projects-model.php";
require_once __DIR__ . "/model/insert-model.php";

// repository
require_once __DIR__ . "/repository/repository-projects.php";

// controller
require_once __DIR__ . "/controller/project-controller.php";
require_once __DIR__ . "/controller/project-setting-controller.php";
require_once __DIR__ . "/controller/project-controller-api.php";

// Helper
require_once __DIR__ . "/helper/find-user.php";


// add
$projects = new Controller\AddProjects();
$projects->init_page();

// Edit
$edit_projects = new Controller\EditProjectsController();
$edit_projects->init_page();

// Api
$api_projects = new Controller\ProjectsApi();
$api_projects->init_api();

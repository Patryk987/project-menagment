<?php

namespace Issues;


// Model
require_once __DIR__ . "/model/issues-model.php";

// Enums
require_once __DIR__ . "/enums/issues-status-enums.php";


// repository
require_once __DIR__ . "/repository/repository-issues.php";

// controller
require_once __DIR__ . "/controller/issues-api-controller.php";
require_once __DIR__ . "/controller/issues-controller.php";


// Init pages
new \Issues\Controller\IssuesController();

// Init api
new \Issues\Controller\IssuesApiController();

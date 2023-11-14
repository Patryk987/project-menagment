<?php

namespace Files;

// Model
require_once __DIR__ . "/model/file-model.php";

// repository
require_once __DIR__ . "/repository/repository-files.php";

// controller
require_once __DIR__ . "/controller/files.php";


// Init pages
new \Files\Controller\FilesNotepadsController();

// Init api


<?php

namespace Files;

define('FILE_ENCRYPTION_BLOCKS', 10000);

// Helper
require_once __DIR__ . "/helper/encrypt-file.php";
require_once __DIR__ . "/helper/decrypt-file.php";
require_once __DIR__ . "/helper/ftp.php";

// Model
require_once __DIR__ . "/model/file-model.php";

// repository
require_once __DIR__ . "/repository/repository-files.php";
require_once __DIR__ . "/repository/repository-share-files.php";

// controller
require_once __DIR__ . "/controller/files.php";
require_once __DIR__ . "/controller/share-file-controller.php";


// Init pages
new \Files\Controller\FilesNotepadsController();
$file_share = new \Files\Controller\FilesShareController();
$file_share->init_page();

// Init api


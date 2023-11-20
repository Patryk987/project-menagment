<?php

namespace Notes;

// Model
require_once __DIR__ . "/model/notepad-model.php";
require_once __DIR__ . "/model/notepad-model.php";
require_once __DIR__ . "/model/notepad-model.php";
require_once __DIR__ . "/model/notepad-model.php";
require_once __DIR__ . "/model/notepad-model.php";

// repository
require_once __DIR__ . "/repository/repository-notes.php";
require_once __DIR__ . "/repository/repository-notepad.php";

// controller
require_once __DIR__ . "/controller/notes.php";
require_once __DIR__ . "/controller/notepads.php";
require_once __DIR__ . "/controller/notes-api-controller.php";
require_once __DIR__ . "/controller/notepads-api-controller.php";


// Init pages
new \Notes\Controller\NotesController();
new \Notes\Controller\NotepadsController();

// Init api
new \Notes\Controller\NotesApiController();
new \Notes\Controller\NotepadsApiController();

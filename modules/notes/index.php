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
require_once __DIR__ . "/controller/notes-api-controller.php";


// Init
new \Notes\Controller\NotesController();
new \Notes\Controller\NotesApiController();

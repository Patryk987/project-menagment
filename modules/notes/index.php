<?php

namespace Notes;

// Model
require_once __DIR__ . "/model/notes-model.php";

// repository
require_once __DIR__ . "/repository/repository-notes.php";

// controller
require_once __DIR__ . "/controller/notes.php";

// Init
new Controller\NotesController();

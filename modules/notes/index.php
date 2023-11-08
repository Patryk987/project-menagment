<?php

namespace Notes;

// Model
require_once __DIR__ . "/model/notes-model.php";

// repository
require_once __DIR__ . "/repository/repository-notes.php";

// controller
require_once __DIR__ . "/controller/notes.php";

// Init
$database_notes_table = file_get_contents(__DIR__ . '/assets/database-config.json');
$database_notes_table = html_entity_decode($database_notes_table);
$database_notes_table = json_decode($database_notes_table, true);

\ModuleManager\DatabaseConnect::set_database_fragment($database_notes_table["note"]);

new Controller\NotesController();

<?php

namespace Files;

// extends database
$database_files_table = file_get_contents(__DIR__ . '/assets/database-config.json');
$database_files_table = html_entity_decode($database_files_table);
$database_files_table = json_decode($database_files_table, true);

\ModuleManager\DatabaseConnect::set_database_fragment($database_files_table["tasks"]);
<?php

namespace Notes\Controller;

class NotesController
{

    use \ModuleManager\LoadFile;

    public function __construct()
    {
        $main_page = [
            "name" => "Notes",
            "link" => "notes",
            "function" => [$this, "notes"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/../notes/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => true
        ];
        \ModuleManager\Pages::set_modules($main_page);
    }

    public function notes()
    {
        return $this->get_page(__DIR__ . "/../view/notes.html");
    }


}

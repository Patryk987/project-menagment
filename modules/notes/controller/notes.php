<?php

namespace Notes\Controller;

class NotesController
{

    use \ModuleManager\LoadFile;

    private $repository;
    private $project_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {
            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->repository = new \Notes\Repository\NotepadRepository();

            $main_page = [
                "name" => \ModuleManager\Main::$translate->get_text("Notes"),
                "link" => "notes",
                "function" => [$this, "notes"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../notes/assets/img/icon.svg",
                "position" => 2,
                "belongs_to_project" => true
            ];
            \ModuleManager\Pages::set_modules($main_page);
        }
    }

    public function notes()
    {

        $all_notepads = $this->repository->get_all($this->project_id);

        $header = [
            \ModuleManager\Main::$translate->get_text("Name") => ["name"],
            \ModuleManager\Main::$translate->get_text("Create date") => ["create_time"],
            \ModuleManager\Main::$translate->get_text("Update date") => ["update_time"]
        ];

        $table = new \ModuleManager\Table(50);

        $table->set_id("notepad_id");

        return $table->generate_table($all_notepads->get_message(), $header);

    }

}

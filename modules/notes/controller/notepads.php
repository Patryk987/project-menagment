<?php

namespace Notes\Controller;

class NotepadsController
{

    use \ModuleManager\LoadFile;
    private $repository;
    private $project_id;

    public function __construct()
    {

        $this->project_id = \ModuleManager\Pages::$project->get_project_id();

        $this->repository = new \Notes\Repository\NotepadRepository();

        $main_page = [
            "name" => "+ Add notepads",
            "link" => "add_notepads",
            "function" => [$this, "add_notepads"],
            "parent_link" => "notes",
            "show" => true
        ];

        \ModuleManager\Pages::set_child_modules($main_page);


        $all_notepads = $this->repository->get_all($this->project_id);

        if ($all_notepads->get_status() == \ApiStatus::CORRECT) {
            foreach ($all_notepads->get_message() as $key => $value) {
                $main_page = [
                    "name" => $value["name"],
                    "link" => $value["name"],
                    "function" => [$this, "notes"],
                    "parent_link" => "notes",
                    "show" => true,
                    "position" => 1
                ];

                \ModuleManager\Pages::set_child_modules($main_page);
            }
        }
    }

    public function add_notepads()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $data = [
                "project_id" => $this->project_id,
                "name" => $_POST['name'],
                "user_id" => \ModuleManager\Main::$token['payload']->user_id
            ];

            $this->repository->create($data);

        }
        return $this->get_page(__DIR__ . "/../view/add-notes.html");
    }
    public function notes()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_project_style", "style" => "/modules/notes/assets/css/style.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "draggable_flex", "script" => "/modules/notes/assets/js/drag-flex.js"]);
        \InjectJavaScript::set_script(["name" => "draggable_grid", "script" => "/modules/notes/assets/js/drag-grid.js"]);
        \InjectJavaScript::set_script(["name" => "notes_script", "script" => "/modules/notes/assets/js/script.js"]);
        \InjectJavaScript::set_script(["name" => "open_note", "script" => "/modules/notes/assets/js/open-note.js"]);

        return $this->get_page(__DIR__ . "/../view/notes.html");
    }


}

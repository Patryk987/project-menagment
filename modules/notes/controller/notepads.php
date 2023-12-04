<?php

namespace Notes\Controller;

class NotepadsController
{

    use \ModuleManager\LoadFile;
    private $repository;
    private $project_id = -1;
    private $notepad_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project)) {
            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->notepad_id = $this->get_notepads_id();
        }

        $this->repository = new \Notes\Repository\NotepadRepository();

        $main_page = [
            "name" => "+ Add notepads",
            "link" => "add_notepads",
            "function" => [$this, "add_notepads"],
            "parent_link" => "notes",
            "show" => true
        ];

        \ModuleManager\Pages::set_child_modules($main_page);

        // 

        \ModuleManager\DataBinder::set_binder(
            [
                "key" => "notepads_title",
                "function" => [$this, "get_notepads_title"]
            ]
        );



        $all_notepads = $this->repository->get_all($this->project_id);

        if ($all_notepads->get_status() == \ApiStatus::CORRECT) {
            foreach ($all_notepads->get_message() as $key => $value) {
                $main_page = [
                    "name" => $value["name"],
                    "link" => $value["notepad_id"],
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
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/notes/assets/js/elements.js"]);

        \InjectJavaScript::set_script(["name" => "draggable_flex", "src" => "/modules/notes/assets/js/drag-flex.js"]);
        \InjectJavaScript::set_script(["name" => "draggable_grid", "src" => "/modules/notes/assets/js/drag-grid.js"]);
        \InjectJavaScript::set_script(["name" => "notes_script", "src" => "/modules/notes/assets/js/script.js"]);
        \InjectJavaScript::set_script(["name" => "open_note", "src" => "/modules/notes/assets/js/open-note.js"]);

        \InjectJavaScript::set_script(
            [
                "name" => "load_notes",
                "type" => "script",
                "script" => "
                    async function load() {
                        var note = new Note(" . $this->notepad_id . ", " . $this->project_id . ");
                        data = await note.get_notes();
                        var grid = new Grid(50);
                        grid.load(data);
                        note.active();
                    }

                    load();
                "
            ]
        );

        return $this->get_page(__DIR__ . "/../view/notes.html");
    }


    public function get_notepads_title()
    {
        $project_data = $this->repository->get_by_id($this->notepad_id);
        return $project_data->get_message()[0]['name'];
    }

    public function get_notepads_id()
    {

        return end(\ModuleManager\Main::$sub_pages);

    }



}

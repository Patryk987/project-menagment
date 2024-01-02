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
            "position" => 2,
            "belongs_to_project" => true
        ];
        \ModuleManager\Pages::set_modules($main_page);
    }

    public function notes()
    {

        // Add style
        // \InjectStyles::set_style(["name" => "add_project_style", "style" => "/modules/notes/assets/css/style.css"]);
        // \InjectStyles::set_style(["name" => "add_dashboard_style", "style" => "/panel-template/css/dashboard.css"]);

        // Add js script
        // \InjectJavaScript::set_script(["name" => "draggable_flex", "src" => "/modules/notes/assets/js/drag-flex.js"]);
        // \InjectJavaScript::set_script(["name" => "draggable_grid", "src" => "/modules/notes/assets/js/drag-grid.js"]);
        // \InjectJavaScript::set_script(["name" => "notes_script", "src" => "/modules/notes/assets/js/script.js"]);
        // \InjectJavaScript::set_script(["name" => "open_note", "src" => "/modules/notes/assets/js/open-note.js"]);


        return $this->get_page(__DIR__ . "/../view/main-page.html");
    }

}

<?php

namespace Projects\Controller;

use Projects\ProjectsRepository;

class EditProjectsController
{

    use \ModuleManager\LoadFile;

    private $project_id;
    private $sedjm;

    public function init_page()
    {
        global $main;
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->sedjm = $main->sedjm;


            $main_page = [
                "name" => "Edit project",
                "link" => "edit_project",
                "function" => [$this, "edit_project"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../projects/assets/img/icon.svg",
                "position" => 999,
                "belongs_to_project" => true
            ];
            \ModuleManager\Pages::set_modules($main_page);

            $main_page = [
                "name" => "Add collaborators",
                "link" => "add_collaborators",
                "function" => [$this, "add_collaborators"],
                "parent_link" => "edit_project",
                "show" => true
            ];

            \ModuleManager\Pages::set_child_modules($main_page);


        }

    }

    public function edit_project()
    {

        return $this->edit_collaborators();
    }

    public function edit_collaborators()
    {

        global $main;

        // Add style
        // \InjectStyles::set_style(["name" => "add_task_style", "style" => "/modules/task/assets/css/style.css"]);

        // Add js script
        // \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/task/assets/js/elements.js"]);

        // $url = __DIR__ . "/../view/edit_collaborators.html";
        // return $this->get_page($url);
        $this->sedjm->clear_all();
        $this->sedjm->set_join(
            "LEFT",
            [
                'table' => "Users",
                'column' => "user_id"
            ],
            [
                'table' => "Collaborators",
                'column' => "user_id"
            ],
        );
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $collaborators = $this->sedjm->get(
            [
                'collaborator_id',
                [
                    "column" => "nick",
                    "alias" => "nick",
                    "table" => "Users"
                ],
                [
                    "column" => "email",
                    "alias" => "email",
                    "table" => "Users"
                ],
            ], "Collaborators");

        $header = [
            "Nick" => ["nick"],
            "E-mail" => ["email"],
        ];

        $table = new \ModuleManager\Table(50);

        // Button
        // $table->add_button("/add_collaborators", "", "Add collaborators");

        $table->set_id("collaborator_id");

        return $table->generate_table($collaborators, $header);
    }

    public function add_collaborators()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_task_style", "style" => "/modules/projects/assets/css/collaborators.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/projects/assets/js/collaborators.js"]);
        \InjectJavaScript::set_script(["name" => "script", "type" => "script", "script" => "
            let collaborators = new Collaborators('" . $this->project_id . "');
            collaborators.activeFindCollaborators();
        "]);

        $link = __DIR__ . "/../view/edit_collaborators.html";
        return $this->get_page($link);
    }

}

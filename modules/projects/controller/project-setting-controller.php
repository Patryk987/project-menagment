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


            if (\ModuleManager\Pages::$project->get_owner_id() == \ModuleManager\Main::$token['payload']->user_id) {

                $this->project_id = \ModuleManager\Pages::$project->get_project_id();
                $this->sedjm = $main->sedjm;

                $main_page = [
                    "name" => \ModuleManager\Main::$translate->get_text("Edit project"),
                    "link" => "project",
                    "function" => [$this, "edit_project"],
                    // "function" => [$this, "project_homepage"],
                    "permission" => [1, 11],
                    "status" => true,
                    "icon" => basename(__DIR__) . "/../projects/assets/img/icon.svg",
                    "position" => 999,
                    "belongs_to_project" => true
                ];
                \ModuleManager\Pages::set_modules($main_page);


                // project setting 
                // $main_page = [
                //     "name" => "Edit project",
                //     "link" => "edit_project",
                //     "function" => [$this, "edit_project"],
                //     "parent_link" => "project",
                //     "show" => true
                // ];

                // \ModuleManager\Pages::set_child_modules($main_page);

                // Collaborators
                $main_page = [
                    "name" => \ModuleManager\Main::$translate->get_text("Collaborators list"),
                    "link" => "collaborators_list",
                    "function" => [$this, "collaborators_list"],
                    "parent_link" => "project",
                    "show" => true
                ];

                \ModuleManager\Pages::set_child_modules($main_page);

                $main_page = [
                    "name" => \ModuleManager\Main::$translate->get_text("Add collaborators"),
                    "link" => "add_collaborators",
                    "function" => [$this, "add_collaborators"],
                    "parent_link" => "project",
                    "show" => true
                ];

                \ModuleManager\Pages::set_child_modules($main_page);

            }



        }

    }

    public function project_homepage()
    {

        return "";
    }

    public function edit_project()
    {
        $form = new \ModuleManager\Forms\Forms("multipart/form-data");

        $repository = new ProjectsRepository();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['owner_id'] = \ModuleManager\Main::$token['payload']->user_id;

            if (!empty($_FILES['photo']['name']))
                $data["photo"] = $_FILES['photo'];

            try {
                $repository->update($this->project_id, $data);
            } catch (\Throwable $th) {
                echo $th->__toString();
            }
        }

        $data = $repository->get_by_id($this->project_id);

        $form->set_data([
            "key" => "only_title",
            "name" => \ModuleManager\Main::$translate->get_text("Project details"),
            "type" => "title"
        ]);

        $form->set_data([
            "key" => "name",
            "name" => \ModuleManager\Main::$translate->get_text("Name"),
            "type" => "input",
            "value" => $data['name']
        ]);

        $form->set_data([
            "key" => "photo",
            "name" => \ModuleManager\Main::$translate->get_text("Photo"),
            "type" => "file",
            "value" => $data['photo_url']
        ]);

        $form->set_data([
            "key" => "description",
            "name" => \ModuleManager\Main::$translate->get_text("Description"),
            "type" => "text",
            "value" => $data['description']
        ]);

        // FTP

        $form->set_data([
            "key" => "only_title",
            "name" => \ModuleManager\Main::$translate->get_text("Serwer FTP"),
            "type" => "title",
        ]);

        $form->set_data([
            "key" => "serwer",
            "name" => \ModuleManager\Main::$translate->get_text("Serwer"),
            "type" => "text",
            "value" => $data['serwer']
        ]);

        $form->set_data([
            "key" => "port",
            "name" => \ModuleManager\Main::$translate->get_text("Port"),
            "type" => "number",
            "value" => $data['port']
        ]);

        $form->set_data([
            "key" => "user",
            "name" => \ModuleManager\Main::$translate->get_text("User"),
            "type" => "text",
            "value" => $data['user']
        ]);

        $form->set_data([
            "key" => "password",
            "name" => \ModuleManager\Main::$translate->get_text("Password"),
            "type" => "text",
            "value" => $data['password']
        ]);

        // Project status 

        $form->set_data([
            "key" => "only_title",
            "name" => \ModuleManager\Main::$translate->get_text("Project status"),
            "type" => "title",
        ]);

        $form->set_data([
            "key" => "status",
            "name" => \ModuleManager\Main::$translate->get_text("Project status"),
            "type" => "select",
            "options" => [
                1 => "Active",
                2 => "Archive"
            ],
            "value" => $data['status']
        ]);


        return $form->get_form(\ModuleManager\Main::$translate->get_text("Edit project"), "save");
    }

    public function collaborators_list()
    {

        global $main;


        if (!empty($_GET['type']) && $_GET['type'] == 'delete' && !empty($_GET['id'])) {
            $this->sedjm->clear_all();
            $this->sedjm->set_where("user_id", $_GET['id'], "=");
            $this->sedjm->set_where("project_id", $this->project_id, "=");
            $this->sedjm->delete("Collaborators");
            $this->sedjm->clear_all();

        }


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
                'user_id',
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
            ],
            "Collaborators"
        );

        $header = [
            "Nick" => ["nick"],
            "E-mail" => ["email"]
        ];

        $table = new \ModuleManager\Table(50);

        // Button
        $table->add_button("/add_collaborators", "", "Add collaborators");

        $table->set_action("", 'delete', 'delete');

        $table->set_id("user_id");

        return $table->generate_table($collaborators, $header);
    }

    public function add_collaborators()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_task_style", "style" => "/modules/projects/assets/css/collaborators.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/projects/assets/js/collaborators.js"]);
        \InjectJavaScript::set_script([
            "name" => "script",
            "type" => "script",
            "script" => "
            let collaborators = new Collaborators('" . $this->project_id . "');
            collaborators.activeFindCollaborators();
        "
        ]);

        $link = __DIR__ . "/../view/edit-collaborators.html";
        return $this->get_page($link);
    }

}

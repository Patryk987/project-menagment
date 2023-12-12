<?php

namespace Projects\Controller;

use Projects\ProjectsRepository;

class AddProjects
{

    use \ModuleManager\LoadFile;

    public function init_page()
    {

        $main_page = [
            "name" => "Add project",
            "link" => "add_project",
            "function" => [$this, "add_project"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/../projects/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => false
        ];
        \ModuleManager\Pages::set_modules($main_page);

    }

    public function add_project()
    {
        global $main;

        // Style
        \InjectStyles::set_style(["name" => "add_project_style", "style" => "/modules/projects/assets/css/style.css"]);
        \InjectStyles::set_style(["name" => "animation", "style" => "/modules/projects/assets/css/animation.css"]);

        // script
        \InjectJavaScript::set_script(["name" => "auto_reload", "src" => "/modules/projects/assets/js/script.js"]);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                "owner_id" => $main::$token['payload']->user_id,
                "name" => $_POST['name'],
                "description" => $_POST['description'],
                "photo" => $_FILES['photo'],
                "serwer" => $_POST['serwer'],
                "user" => $_POST['user'],
                "password" => $_POST['password'],
                "port" => $_POST['port'],
            ];
            $project_repository = new ProjectsRepository();
            $project_repository->create($data);
        }

        return $this->get_page(__DIR__ . "/../view/add-project.html");
    }

    // Private

    private function insert_new()
    {

    }


}
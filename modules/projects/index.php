<?php

namespace Projects;

// Model
require_once __DIR__ . "/model/projects-model.php";
require_once __DIR__ . "/model/insert-model.php";

// repository
require_once __DIR__ . "/repository/repository-projects.php";

// service
require_once __DIR__ . "/service/add-project.php";

class Projects
{


    private $data;

    function __construct()
    {

        $add_project = new AddProjects();

    }

    public function init_page()
    {

    }


    public function home()
    {
        return "";
    }

}

$projects = new Projects;


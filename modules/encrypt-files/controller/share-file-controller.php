<?php

namespace Files\Controller;

use Files\Repository as Repository;

class FilesShareController
{

    use \ModuleManager\LoadFile;
    private $repository;
    private $project_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        }
    }

    public function init_page()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $main_page = [
                "name" => "Shared files",
                "link" => "shared_files",
                "function" => [$this, "shared_files_list"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../encrypt-files/assets/img/share-icon.svg",
                "position" => 4,
                "belongs_to_project" => true
            ];
            \ModuleManager\Pages::set_modules($main_page);

            $main_page = [
                "name" => "Share file",
                "link" => "share_file",
                "function" => [$this, "share_file"],
                "parent_link" => "shared_files",
                "show" => true,
                "position" => 1
            ];

            \ModuleManager\Pages::set_child_modules($main_page);

        }

    }

    public function shared_files_list()
    {

        return "Share files";

    }

    public function share_file()
    {

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

        }

        $form = new \ModuleManager\Forms\Forms();

        $form->set_data([
            "key" => "life_time",
            "name" => "Life time (days)",
            "type" => "number"
        ]);

        $form->set_data([
            "key" => "email",
            "name" => "Recipient e-mail",
            "type" => "email"
        ]);

        $form->set_data([
            "key" => "file",
            "name" => "file",
            "type" => "file"
        ]);


        return $form->get_form("Send file", "Send");

    }


}

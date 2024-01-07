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
        global $main;
        $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        $this->repository = new Repository\FilesShareRepository($this->project_id);

        if (!empty($_GET['type']) && $_GET['type'] == "delete" && !empty($_GET['id'])) {
            if ($this->repository->connect_to_ftp()) {
                if ($this->repository->delete_share_file($_GET['id'])) {
                    $main->popups->add_popup("Poprawnie usunięto plik", "", "correct");
                } else {
                    $main->popups->add_popup("Nie udało się usunąć pliku", "spróbuj ponownie", "error");
                }
                $main->popups->show_popups();

            }

        }



        $header = [
            "Recipient" => ["recipient_email"],
            "Upload time" => ["upload_time"],
            "Life time" => ["life_time"],
            "File name" => ["file_name"]
        ];



        $table = new \ModuleManager\Table(50);
        $table->set_converter("life_time", [$this, "unix_to_day"]);
        $table->set_converter("upload_time", ["Helper", "time_to_data"]);

        $table->set_action("", 'delete', 'delete');

        $table->set_id("share_file_id");

        return $table->generate_table($this->repository->list_share_files(), $header);

    }

    public function share_file()
    {
        $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        $this->repository = new Repository\FilesShareRepository($this->project_id);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $this->repository->share_file($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], $_POST['email'], $_POST['life_time']);

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

    public function delete_share_file()
    {

    }

    public function unix_to_day($value)
    {
        return ($value / 86400) . " days ";
    }


}

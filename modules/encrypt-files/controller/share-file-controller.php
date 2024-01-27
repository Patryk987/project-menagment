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
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "home", 2, 4);
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        }
    }

    public function empty_block()
    {
        \InjectStyles::set_style(["name" => "shared_file_style", "style" => "/modules/home/assets/css/shared_file.css"]);

        $this->repository = new Repository\FilesShareRepository();
        $file_list = $this->repository->get_user_shared_file(\ModuleManager\Main::$token['payload']->user_id);

        $header = [
            "File name" => ["file_name"],
            "Upload time" => ["upload_time"],
            "Life time" => ["life_time"],
        ];

        $table = new \ModuleManager\Table(50);
        $table->set_converter("life_time", [$this, "unix_to_day"]);
        $table->set_converter("upload_time", ["Helper", "time_to_data"]);

        $table->set_id("share_file_id");
        $data = $table->generate_table($file_list, $header);
        $html = "<div class='shared_file'><h2>Pliki udostępnione dla ciebie</h2>" . $data . "</div>";
        return $html;
    }

    public function init_page()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $main_page = [
                "name" => \ModuleManager\Main::$translate->get_text("Shared files"),
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
                "name" => \ModuleManager\Main::$translate->get_text("Share file"),
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
            \ModuleManager\Main::$translate->get_text("Recipient") => ["recipient_email"],
            \ModuleManager\Main::$translate->get_text("Upload time") => ["upload_time"],
            \ModuleManager\Main::$translate->get_text("Life time") => ["life_time"],
            \ModuleManager\Main::$translate->get_text("File name") => ["file_name"]
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
        global $main;

        $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        $this->repository = new Repository\FilesShareRepository($this->project_id);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $results = $this->repository->share_file($_FILES['file']['tmp_name'], $_FILES['file']['name'], $_FILES['file']['type'], $_POST['email'], $_POST['life_time']);

            if ($results->get_status() == \ApiStatus::CORRECT) {
                $main->popups->add_popup("Poprawnie dodano plik", "", "correct");
            } else {
                foreach ($results->get_error() as $error) {
                    switch ($error) {
                        case 'empty_data':
                            $main->popups->add_popup("Nie udało się dodać pliku", "uzupełnij wszystkie dane", "error");
                            break;
                        case 'incorrect_email':
                            $main->popups->add_popup("Nie udało się dodać pliku", "Nie znaleziono użytkownika w naszej bazie", "error");
                            break;

                        default:
                            $main->popups->add_popup("Nie udało się dodać pliku", "spróbuj ponownie", "error");
                            break;
                    }
                }
            }
            $main->popups->show_popups();
        }

        $form = new \ModuleManager\Forms\Forms();

        $form->set_data([
            "key" => "life_time",
            "name" => \ModuleManager\Main::$translate->get_text("Life time (days)"),
            "type" => "number"
        ]);

        $form->set_data([
            "key" => "email",
            "name" => \ModuleManager\Main::$translate->get_text("Recipient e-mail"),
            "type" => "email"
        ]);

        $form->set_data([
            "key" => "file",
            "name" => \ModuleManager\Main::$translate->get_text("File name"),
            "type" => "file"
        ]);


        return $form->get_form(\ModuleManager\Main::$translate->get_text("Send file"), \ModuleManager\Main::$translate->get_text("Send"));

    }

    public function unix_to_day($value)
    {
        return ($value / 86400) . " days ";
    }


}

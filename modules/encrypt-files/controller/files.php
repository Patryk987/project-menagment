<?php

namespace Files\Controller;

class FilesNotepadsController
{

    use \ModuleManager\LoadFile;
    private $repository;
    private $project_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project)) {
            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
        }

        $main_page = [
            "name" => "Files",
            "link" => "files",
            "function" => [$this, "files"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/../encrypt-files/assets/img/icon.svg",
            "position" => 3,
            "belongs_to_project" => true
        ];
        \ModuleManager\Pages::set_modules($main_page);

    }

    public function files()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_project_style", "style" => "/modules/encrypt-files/assets/css/style.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/encrypt-files/assets/js/script.js"]);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            encryptFile($_FILES['file']['tmp_name'], 'my-secret-key', './modules/encrypt-files/helper/test.encrypt');
            // chmod('./modules/encrypt-files/helper/' . $_FILES['file']['name'], 0777);
            decryptFile('./modules/encrypt-files/helper/test.encrypt', 'my-secret-key', './modules/encrypt-files/helper/' . $_FILES['file']['name']);
            // chmod('./modules/encrypt-files/helper/d-' . $_FILES['file']['name'], 0777);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename('./modules/encrypt-files/helper/' . $_FILES['file']['name']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize('./modules/encrypt-files/helper/' . $_FILES['file']['name']));
            flush(); // Flush system output buffer
            readfile('./modules/encrypt-files/helper/' . $_FILES['file']['name']);
            die();
        }



        return $this->get_page(__DIR__ . "/../view/files.html");
    }

}

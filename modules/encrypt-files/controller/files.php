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
        $this->repository = new \Files\Repository\FilesRepository($this->project_id);
        // Add style
        \InjectStyles::set_style(["name" => "add_project_style", "style" => "/modules/encrypt-files/assets/css/style.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/encrypt-files/assets/js/script.js"]);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $this->repository->upload_file($_FILES['file']['tmp_name'], $_GET['pwd'], $_FILES['file']['name'], $_FILES['file']['type']);

        }


        $files_header = [
            "Nazwa" => ["name"],
            "Data modyfikacji" => ["modify_time"],
            "Rozmiar" => ["size"]
        ];
        $form = '
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="file" value="add file" />
                <input type="submit" />
            </form>
        ';

        $table = new \ModuleManager\Table(500);

        $view = $form;
        $view .= $table->generate_table($this->get_files(), $files_header);
        return $view;

    }

    private function parse_size($size): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        if ($size > 0) {
            $exp = floor(log($size, 1024));
            $convert_unit = $size / (pow(1024, $exp));
            $unit = $units[$exp];

            return sprintf('%.2f %s', $convert_unit, $unit);
        } else {
            return '0 B';
        }
    }

    private function get_files(): array
    {

        $directory = !empty($_GET["pwd"]) ? $_GET["pwd"] : ".";

        $data = [];

        if (!empty($_GET["loca_file"])) {
            $this->repository->download_file($_GET["loca_file"], $_GET["name"]);
        }

        foreach ($this->repository->list_file($directory) as $key => $value) {
            if ($value['type'] == "directory")
                $name = "<a href='?pwd=" . $value["pwd"] . "/" . $value["name"] . "'>" . $value["name"] . "</a>";
            else
                $name = "<a href='?pwd=" . $value["pwd"] . "/" . $value["name"] . "&loca_file=" . $value["pwd"] . "/" . $value["name"] . "&name=" . $value["name"] . "'>" . $value["name"] . "</a>";


            $data[] = [
                "name" => $name,
                "modify_time" => $value["modify_time"],
                "size" => $this->parse_size($value["size"])
            ];
        }

        return $data;

    }

}

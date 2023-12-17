<?php

namespace Issues\Controller;

use \ModuleManager\Forms\Forms;
use \Issues\Enums\IssuesStatus;

class IssuesController
{

    use \ModuleManager\LoadFile;
    private $repository;
    private $project_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->repository = new \Issues\Repository\IssuesRepository($this->project_id);

            $main_page = [
                "name" => "Issues",
                "link" => "issues",
                "function" => [$this, "list_issues"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../issues/assets/img/icon.svg",
                "position" => 4,
                "belongs_to_project" => true
            ];

            \ModuleManager\Pages::set_modules($main_page);

            // Child 

            $main_page = [
                "name" => "Add issues",
                "link" => "add_issues",
                "function" => [$this, "add_issues"],
                "parent_link" => "issues",
                "show" => true
            ];

            \ModuleManager\Pages::set_child_modules($main_page);

        }

    }

    public function add_issues()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !empty($_POST['title'])
            ) {

                $data = [
                    "project_id" => $this->project_id,
                    "author_id" => \ModuleManager\Main::$token['payload']->user_id,
                    "title" => $_POST['title'],
                    "description" => !empty($_POST['description']) ? $_POST['description'] : null,
                ];

                $response = $this->repository->create($data);

            } else {

            }
        }

        $form = new \ModuleManager\Forms\Forms("multipart/form-data");

        $form->set_data([
            "key" => "title",
            "name" => "Title",
            "type" => "input"
        ]);

        $form->set_data([
            "key" => "description",
            "name" => "Description",
            "type" => "textarea"
        ]);

        return $form->get_form("Edit project", "save");
    }

    public function list_issues()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_file_style", "style" => "/modules/issues/assets/css/style.css"]);

        $issues = $this->repository->get_all();
        // $issues = [];
        // for ($i = 0; $i < 1000000; $i++) {
        //     $issues[] = [
        //         "title" => $i . "title",
        //         "create_date" => $i . "create_date",
        //         "status" => $i . "status",
        //     ];
        // }
        $header = [
            "Tytuł" => ["title"],
            "Create date" => ["create_date"],
            "Status" => ["status"]
        ];
        $table = new \ModuleManager\Table(50);
        $table->set_converter("status", [$this, "convert_status"]);
        // return $table->generate_table($issues, $header);
        return $table->generate_table($issues->get_message(), $header);
    }

    public function convert_status($value)
    {

        $value = IssuesStatus::from($value);
        $class = $value->get_class();

        $html = '<div class="' . $class . '">';
        $html .= $value->get_name();
        $html .= '</div>';

        return $html;
    }



}

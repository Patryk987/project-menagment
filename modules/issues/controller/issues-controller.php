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
                "name" => \ModuleManager\Main::$translate->get_text("Issues"),
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
                "name" => \ModuleManager\Main::$translate->get_text("Add issues"),
                "link" => "add_issues",
                "function" => [$this, "add_issues"],
                "parent_link" => "issues",
                "show" => true
            ];

            \ModuleManager\Pages::set_child_modules($main_page);

            \ModuleManager\DataBinder::set_binder(
                [
                    "key" => "issues_list",
                    "function" => [$this, "issues_list"]
                ]
            );

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

        return $form->get_form(\ModuleManager\Main::$translate->get_text("Edit project"), "save");
    }

    public function list_issues()
    {

        // Add style
        \InjectStyles::set_style(["name" => "add_file_style", "style" => "/modules/issues/assets/css/style.css"]);
        \InjectStyles::set_style(["name" => "single_note_style", "style" => "/modules/issues/assets/css/single-note.css"]);

        // script
        \InjectJavaScript::set_script(["name" => "issues_element", "src" => "/modules/issues/assets/js/elements.js"]);
        \InjectJavaScript::set_script(["name" => "issues_repository_script", "src" => "/modules/issues/assets/js/issues-repository.js"]);
        \InjectJavaScript::set_script(["name" => "details_script", "src" => "/modules/issues/assets/js/open-note.js"]);
        \InjectJavaScript::set_script(["name" => "issues_script", "src" => "/modules/issues/assets/js/script.js"]);

        \InjectJavaScript::set_script(
            [
                "name" => "issues",
                "type" => "script",
                "script" => "
                    var issues = new Issues(" . $this->project_id . ");
                    issues.active();
                "
            ]
        );

        return $this->get_page(__DIR__ . "/../view/issues.html");

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


    public function issues_list()
    {
        $issues = $this->repository->get_all();
        $header = [
            "Tytuł" => ["title"],
            \ModuleManager\Main::$translate->get_text("Create date") => ["create_date"],
            "Status" => ["status"]
        ];
        $table = new \ModuleManager\Table(50);
        $table->set_converter("status", [$this, "convert_status"]);
        $table->set_id("issues_id");
        // return $table->generate_table($issues, $header);
        return $table->generate_table($issues->get_message(), $header);
    }


}

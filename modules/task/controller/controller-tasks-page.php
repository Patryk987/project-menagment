<?php

namespace Tasks\Controller;

use Tasks\Repository as Repository;
use ModuleManager\Forms\Forms;
use Tasks\Enums as Enums;

class TasksPageController
{

    use \ModuleManager\LoadFile;

    private Repository\TasksGroupRepository $task_group_repository;
    private Repository\TasksRepository $task_repository;
    private int $project_id = -1;
    private $task_group_id = -1;

    public function __construct()
    {
        if (!empty(\ModuleManager\Pages::$project) && \ModuleManager\Pages::$project->get_status() != \ProjectStatus::BLOCKED) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->task_group_id = $this->get_task_group_id();

            $this->task_group_repository = new Repository\TasksGroupRepository($this->project_id);

            $main_page = [
                "name" => \ModuleManager\Main::$translate->get_text("Tasks"),
                "link" => "task",
                "function" => [$this, "task"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../task/assets/img/icon.svg",
                "position" => 5,
                "belongs_to_project" => true
            ];
            \ModuleManager\Pages::set_modules($main_page);

            $main_page = [
                "name" => \ModuleManager\Main::$translate->get_text("+ Add group"),
                "link" => "add_task_group",
                "function" => [$this, "add_group"],
                "parent_link" => "task",
                "show" => true,
                "position" => 999
            ];

            \ModuleManager\Pages::set_child_modules($main_page);

            $groups = $this->task_group_repository->get_all();

            // if ($groups->get_status() == \ApiStatus::CORRECT) {
            foreach ($groups as $key => $value) {
                $main_page = [
                    "name" => $value["group_name"],
                    "link" => $value["task_group_id"],
                    "function" => [$this, "task_group"],
                    "parent_link" => "task",
                    "show" => true,
                    "position" => 1
                ];

                \ModuleManager\Pages::set_child_modules($main_page);
            }
            // }

            // BINDER ??? 

            \ModuleManager\DataBinder::set_binder(
                [
                    "key" => "task_group_title",
                    "function" => [$this, "get_task_group_title"]
                ]
            );


        }



    }

    public function add_group()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                "author_id" => \ModuleManager\Main::$token['payload']->user_id,
                "group_name" => $_POST['name'],
                "group_description" => $_POST['description'],
                "color" => $_POST['color']
            ];
            $this->task_group_repository->create($data);
        }

        $form = new Forms();

        $form->set_data([
            "key" => "name",
            "name" => "Group name",
            "type" => "input"
        ]);

        $form->set_data([
            "key" => "description",
            "name" => "Group description",
            "type" => "textarea"
        ]);

        $form->set_data([
            "key" => "color",
            "name" => "Group color",
            "type" => "color"
        ]);

        return $form->get_form(\ModuleManager\Main::$translate->get_text("Add tasks group"), \ModuleManager\Main::$translate->get_text("Add"));

    }

    public function task()
    {
        $groups = $this->task_group_repository->get_all();
        $header = [
            \ModuleManager\Main::$translate->get_text("Name") => ["group_name"],
            \ModuleManager\Main::$translate->get_text("Create date") => ["create_date"],
            \ModuleManager\Main::$translate->get_text("Color") => ["color"]
        ];

        $table = new \ModuleManager\Table(50);
        $table->set_converter("color", [$this, "color_show"]);
        $table->set_id("task_group_id");

        return $table->generate_table($groups, $header);
    }

    public function task_group()
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_task_style", "style" => "/modules/task/assets/css/style.css"]);
        \InjectStyles::set_style(["name" => uniqid(), "style" => "/modules/task/assets/css/other.css"]);
        \InjectStyles::set_style(["name" => 'single-note', "style" => "/modules/task/assets/css/single-note.css"]);

        // Blocks
        \InjectJavaScript::set_script(["name" => "load_js_elements", "src" => "/modules/task/assets/blocks/elements.js"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "open_note", "src" => "/modules/task/assets/js/open-note.js"]);
        \InjectJavaScript::set_script(["name" => "task_repository", "src" => "/modules/task/assets/js/repository-status-tasks.js"]);
        \InjectJavaScript::set_script(["name" => "load_js_task_repository", "src" => "/modules/task/assets/js/repository-tasks.js"]);
        \InjectJavaScript::set_script(["name" => "load_js_task", "src" => "/modules/task/assets/js/task.js"]);
        \InjectJavaScript::set_script(["name" => "script", "src" => "/modules/task/assets/js/script.js"]);
        \InjectJavaScript::set_script(["name" => "kanban", "src" => "/modules/task/assets/js/kanban.js"]);
        \InjectJavaScript::set_script(["name" => uniqid(), "src" => "/modules/task/assets/js/load-kanban-script.js"]);


        \InjectJavaScript::set_script(
            [
                "name" => "load_task",
                "type" => "script",
                "script" => "
                    var task = new Task(" . $this->task_group_id . ", " . $this->project_id . ");
                    task.active();
                "
            ]
        );

        \InjectJavaScript::set_script(
            [
                "name" => "load_kanban",
                "type" => "script",
                "script" => "
                    var kanban = new loadKanban(" . $this->task_group_id . ", " . $this->project_id . ");
                    kanban.load();
                "
            ]
        );

        return $this->get_page(__DIR__ . "/../view/task.html");
    }

    public function get_task_group_id()
    {

        return end(\ModuleManager\Main::$sub_pages);

    }


    public function get_task_group_title()
    {
        $project_data = $this->task_group_repository->get_by_id($this->task_group_id);
        return $project_data[0]['group_name'];
    }

    public function color_show($color)
    {
        return "<div style='background-color: " . $color . "; border-radius: var(--radius); width: 40px; height: 15px'></div>";
    }

}

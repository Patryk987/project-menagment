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
        if (!empty(\ModuleManager\Pages::$project)) {

            $this->project_id = \ModuleManager\Pages::$project->get_project_id();
            $this->task_group_id = $this->get_task_group_id();

            $this->task_group_repository = new Repository\TasksGroupRepository($this->project_id);

            $main_page = [
                "name" => "Tasks",
                "link" => "task",
                "function" => [$this, "task"],
                "permission" => [1, 11],
                "status" => true,
                "icon" => basename(__DIR__) . "/../task/assets/img/icon.svg",
                "position" => 3,
                "belongs_to_project" => true
            ];
            \ModuleManager\Pages::set_modules($main_page);

            $main_page = [
                "name" => "+ Add group",
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

        return $form->get_form("Add tasks group", "Add");

    }

    public function task()
    {
        return "";
    }

    public function task_group()
    {
        $this->task_repository = new Repository\TasksRepository($this->project_id, $this->get_task_group_id());
        $tasks = $this->task_repository->get_all();
        var_dump($tasks);
        return "task_group";
    }

    public function get_task_group_id()
    {

        return end(\ModuleManager\Main::$sub_pages);

    }

}

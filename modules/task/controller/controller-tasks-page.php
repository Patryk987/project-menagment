<?php

namespace Tasks\Controller;

use Tasks\Repository as Repository;

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
            $this->task_repository = new Repository\TasksRepository($this->project_id);

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

        }


    }

    public function task()
    {
        return "TEST";
    }

    public function get_task_group_id()
    {

        return end(\ModuleManager\Main::$sub_pages);

    }

}

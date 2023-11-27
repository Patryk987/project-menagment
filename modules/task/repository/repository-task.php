<?php

namespace Tasks\Repository;

use Tasks\Enums as Enums;


class TasksRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "Task";
    private int $project_id;
    private int $task_group_id;

    public function __construct($project_id, $task_group_id)
    {
        global $main;
        $this->project_id = $project_id;
        $this->task_group_id = $task_group_id;
        $this->sedjm = $main->sedjm;
    }

    public function get_all()
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        $data = $this->sedjm->get(
            [
                "task_id",
                "task_group_id",
                "author_id",
                "create_date",
                "update_date",
                "task_name",
                "content",
                "end_time",
                "repeat_status",
                "color",
                "task_status_id"
            ],
            $this->table
        );

        return $data;

    }

    public function get_by_id($id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        $data = $this->sedjm->get(
            [
                "task_id",
                "task_group_id",
                "author_id",
                "create_date",
                "update_date",
                "task_name",
                "content",
                "end_time",
                "repeat_status",
                "color",
                "task_status_id"
            ],
            $this->table
        );

        return $data;

    }

    public function create(array $input)
    {
        $data = [
            "task_group_id" => $this->task_group_id,
            "author_id" => $input['author_id'],
            "create_date" => time(),
            "update_date" => time(),
            "task_name" => $input['task_name'],
            "content" => $input['content'],
            "end_time" => $input['end_time'],
            "repeat_status" => $input['repeat_status'],
            "color" => $input['color'],
            "task_status_id" => Enums\TaskStatus::ACTIVE->value
        ];
        $this->sedjm->clear_all();
        $this->sedjm->insert($data, $this->table);

    }

    public function update($id, array $input)
    {
        $data = [
            "update_date" => time(),
            "task_name" => $input['task_name'],
            "content" => $input['content'],
            "end_time" => $input['end_time'],
            "repeat_status" => $input['repeat_status'],
            "color" => $input['color'],
            "task_status_id" => $input['task_status_id']
        ];

        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        $this->sedjm->update($data, $this->table);

    }

    public function delete($id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        $this->sedjm->delete($this->table);

    }

}
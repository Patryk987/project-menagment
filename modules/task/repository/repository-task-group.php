<?php

namespace Tasks\Repository;

use Tasks\Enums as Enums;

class TasksGroupRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "TaskGroup";
    private int $project_id;
    public function __construct($project_id)
    {
        global $main;
        $this->project_id = $project_id;
        $this->sedjm = $main->sedjm;
    }

    public function get_all()
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $data = $this->sedjm->get(
            [
                "task_group_id",
                "project_id",
                "author_id",
                "create_date",
                [
                    "column" => "create_date",
                    "alias" => "create_date",
                    "table" => $this->table,
                    "function" => ["Helper", "time_to_data"]
                ],
                "group_name",
                "group_description",
                "color",
                [
                    "column" => "status",
                    "alias" => "status",
                    "table" => $this->table,
                    "function" => [$this, "get_status_name"]
                ],
            ],
            $this->table
        );

        return $data;

    }

    public function get_by_id($id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_where("task_group_id", $id, "=");
        $data = $this->sedjm->get(
            [
                "task_group_id",
                "project_id",
                "author_id",
                "create_date",
                [
                    "column" => "create_date",
                    "alias" => "create_date",
                    "table" => $this->table,
                    "function" => ["Helper", "time_to_data"]
                ],
                "group_name",
                "group_description",
                "color",
                [
                    "column" => "status",
                    "alias" => "status",
                    "table" => $this->table,
                    "function" => [$this, "get_status_name"]
                ],
            ],
            $this->table
        );

        return $data;

    }

    public function create(array $input)
    {
        $this->sedjm->clear_all();
        $data = [
            "project_id" => $this->project_id,
            "author_id" => $input['author_id'],
            "create_date" => time(),
            "group_name" => $input['group_name'],
            "group_description" => !empty($input['group_description']) ? $input['group_description'] : null,
            "color" => !empty($input['color']) ? $input['color'] : null,
            "status" => Enums\TaskGroupStatus::ACTIVE->value,
        ];

        return $this->sedjm->insert($data, $this->table);
    }

    public function update($id, array $input)
    {
        $data = [
            "group_name" => $input['group_name'],
            "group_description" => $input['group_description'],
            "color" => $input['color'],
            "status" => $input['status'],
        ];

        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_where("task_group_id", $id, "=");
        $this->sedjm->update($data, $this->table);
    }

    public function delete($id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_where("task_group_id", $id, "=");
        $this->sedjm->delete($this->table);
    }

    public static function get_status_name(int $status)
    {
        return Enums\TaskGroupStatus::from($status)->get_name();
    }
}
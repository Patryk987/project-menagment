<?php

namespace Tasks\Repository;

use Tasks\Enums as Enums;


class TasksStatusRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "TaskTags";
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
                "task_group_id",
                "task_tags_id",
                "name",
                "color"
            ],
            $this->table
        );

        return $data;

    }

    public function create(array $input)
    {

        $data = [
            "task_group_id" => $this->task_group_id,
            "name" => $input["name"],
            "color" => $input["color"]
        ];

        $this->sedjm->clear_all();
        return $this->sedjm->insert($data, $this->table);

    }

    public function update($id, array $input)
    {
        $data = [];

        if (!empty($input['name']))
            $data["name"] = $input['name'];

        if (!empty($input['color']))
            $data["color"] = $input['color'];

        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_tags_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");

        return $this->sedjm->update($data, $this->table);

    }

    public function delete($id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_tags_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        return $this->sedjm->delete($this->table);

    }

}
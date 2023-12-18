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
                "task_status_id",
                "task_tag_id"
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
                "task_name",
                "end_time",
                "repeat_status",
                "color",
                "task_status_id",
                "task_tag_id",
                [
                    "column" => "create_date",
                    "alias" => "create_date",
                    "table" => $this->table,
                    "function" => ["helper", "time_to_data"]
                ],
                [
                    "column" => "update_date",
                    "alias" => "update_date",
                    "table" => $this->table,
                    "function" => ["helper", "time_to_data"]
                ],
                [
                    "column" => "author_id",
                    "alias" => "author",
                    "table" => $this->table,
                    "function" => [$this, "get_user_by_id"]
                ],
                [
                    "column" => "content",
                    "alias" => "content",
                    "table" => $this->table,
                    "function" => [$this, "parse_content"]
                ]
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
            "content" => !empty($input['content']) ? $input['content'] : null,
            "end_time" => !empty($input['end_time']) ? $input['end_time'] : null,
            "repeat_status" => !empty($input['repeat_status']) ? $input['repeat_status'] : 0,
            "task_tag_id" => !empty($input['task_tag_id']) ? $input['task_tag_id'] : -1,
            "color" => !empty($input['color']) ? $input['color'] : null,
            "task_status_id" => Enums\TaskStatus::ACTIVE->value
        ];

        $this->sedjm->clear_all();
        return $this->sedjm->insert($data, $this->table);

    }

    public function update($id, array $input)
    {
        $data = [];
        $data["update_date"] = time();

        if (!empty($input['task_name']))
            $data["task_name"] = $input['task_name'];

        if (!empty($input['content']))
            $data["content"] = $input['content'];

        if (!empty($input['end_time']))
            $data["end_time"] = $input['end_time'];

        if (!empty($input['repeat_status']))
            $data["repeat_status"] = $input['repeat_status'];

        if (!empty($input['color']))
            $data["color"] = $input['color'];

        if (!empty($input['task_status_id']))
            $data["task_status_id"] = $input['task_status_id'];

        if (!empty($input['task_tag_id']))
            $data["task_tag_id"] = $input['task_tag_id'];


        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");

        return $this->sedjm->update($data, $this->table);

    }

    public function delete($id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("task_id", $id, "=");
        $this->sedjm->set_where("task_group_id", $this->task_group_id, "=");
        return $this->sedjm->delete($this->table);

    }

    public function get_user_by_id($user_id)
    {
        $this->sedjm->clear_all();

        $table = "Users";
        $additionalTable = "UserData";

        $this->sedjm->set_where("user_id", $user_id, "=");
        $get_data = $this->sedjm->get(["nick", "email", "phone_number"], $table);
        $additional_data = $this->sedjm->get(["field_key", "value"], $additionalTable);

        $additional = [];
        foreach ($additional_data as $key => $value) {

            $additional[$value['field_key']] = $value['value'];

        }

        $output = [
            "status" => true,
            "data" => $get_data[0],
            "additional_data" => $additional
        ];

        return $output;
    }

    public function parse_content($content)
    {
        $content = html_entity_decode($content);
        $content = htmlspecialchars_decode($content);
        return $content;
    }


}
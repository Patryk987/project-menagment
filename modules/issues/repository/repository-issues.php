<?php

namespace Issues\Repository;

use Issues\Enums\IssuesStatus;

class IssuesRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "Issues";
    private $project_id;

    public function __construct($project_id)
    {
        global $main;
        $this->sedjm = $main->sedjm;
        $this->project_id = $project_id;
    }

    public function get_all()
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");

        $data = [
            "issues_id",
            "project_id",
            "author_id",
            [
                "table" => $this->table,
                "column" => 'create_date',
                "alias" => 'create_date',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => $this->table,
                "column" => 'update_date',
                "alias" => 'update_date',
                "function" => ["Helper", "time_to_data"]
            ],
            "title",
            "description",
            "status"
        ];

        $get = $this->sedjm->get($data, $this->table);

        if (count($get) > 0) {
            $status = \ApiStatus::CORRECT;
        } else {
            $status = \ApiStatus::ERROR;
        }

        $output = new \Models\ApiModel($status, $get);
        return $output;
    }

    public function get_by_id($id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_where("issues_id", $id, "=");

        $data = [
            "issues_id",
            "project_id",
            "author_id",
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
            "title",
            "description",
            "status"
        ];

        $get = $this->sedjm->get($data, $this->table);

        if (count($get) > 0) {
            $status = \ApiStatus::CORRECT;
        } else {
            $status = \ApiStatus::ERROR;
        }

        $output = new \Models\ApiModel($status, $get);
        return $output;
    }

    public function create(array $data)
    {

        if (
            !empty($data['project_id'])
            && !empty($data['title'])
        ) {

            $data = [
                "project_id" => $data['project_id'],
                "author_id" => !empty($data['author_id']) ? $data['author_id'] : 0,
                "create_date" => time(),
                "update_date" => time(),
                "title" => $data['title'],
                "description" => !empty($data['description']) ? $data['description'] : null,
                "status" => IssuesStatus::TODO->value
            ];

            $insert = $this->sedjm->insert($data, $this->table);

            $output = new \Models\ApiModel(\ApiStatus::from($insert['status']), ['id' => $insert['id']]);

            return $output;

        } else {
            $output = new \Models\ApiModel(\ApiStatus::ERROR, null, ['Empty data']);
            return $output;
        }
    }

    public function update($id, array $data)
    {
        if (
            !empty($id)
        ) {
            $update_data = [];

            if (!empty($data["title"]))
                $update_data["title"] = $data["title"];

            if (!empty($data["description"]))
                $update_data["description"] = $data["description"];

            if (!empty($data["status"]))
                $update_data["status"] = $data["status"];

            $update_data["update_date"] = time();

            $this->sedjm->clear_all();
            $this->sedjm->set_where("project_id", $this->project_id, "=");
            $this->sedjm->set_where("issues_id", $id, "=");
            $insert = $this->sedjm->update($update_data, $this->table);

            $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
            return $output;

        } else {
            $output = new \Models\ApiModel(\ApiStatus::ERROR, null, ['Empty data']);
            return $output;
        }
    }
    public function delete($id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_where("issues_id", $id, "=");
        $insert = $this->sedjm->delete($this->table);

        $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
        return $output;
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
}
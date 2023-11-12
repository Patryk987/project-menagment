<?php

namespace Notes\Repository;

// interface RepositoryNotepadsInterface extends \RepositoryInterface
// {
//     public function get_all(int $user_id);
// }

class NotepadRepository // implements RepositoryNotepadsInterface
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "Notepad";

    public function __construct()
    {
        global $main;
        $this->sedjm = $main->sedjm;
    }

    public function get_all($project_id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $project_id, "=");

        $data = [
            "notepad_id",
            "project_id",
            "author_id",
            "name",
            "icon_url",
            "background",
            "default_view",
            "create_time",
            "update_time"
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
        $this->sedjm->set_where("notepad_id", $id, "=");

        $data = [
            "notepad_id",
            "project_id",
            "author_id",
            "name",
            "icon_url",
            "background",
            "default_view",
            "create_time",
            "update_time"
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
            && !empty($data['user_id'])
            && !empty($data['name'])
        ) {

            $data = [
                "project_id" => $data['project_id'],
                "author_id" => $data['user_id'],
                "name" => $data['name'],
                "icon_url" => !empty($data['icon_url']) ? $data['icon_url'] : null,
                "background" => !empty($data['background']) ? $data['background'] : null,
                "default_view" => 1,
                "create_time" => time(),
                "update_time" => time(),
            ];

            $insert = $this->sedjm->insert($data, $this->table);

            $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));

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
            $data = [
                "name" => $data['name'],
                "icon_url" => $data['icon_url'],
                "background" => $data['background'],
                "update_time" => time()
            ];

            $this->sedjm->clear_all();
            $this->sedjm->set_where("note_id", $id, "=");
            $insert = $this->sedjm->insert($data, $this->table);

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
        $this->sedjm->set_where("notepad_id", $id, "=");
        $insert = $this->sedjm->delete($this->table);

        $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
        return $output;
    }
}
<?php

namespace Notes\Repository;

interface RepositoryNotesInterface extends \RepositoryInterface {
    public function get_all_notepads_notes(int $notepad_id);
}

class NotesRepository implements RepositoryNotesInterface {

    private \ModuleManager\SEDJM $sedjm;
    private $table = "Notes";

    public function __construct() {
        global $main;
        $this->sedjm = $main->sedjm;
    }

    public function get_all() {

        $this->sedjm->clear_all();

        $data = [
            "note_id",
            "notepad_id",
            "author_id",
            "title",
            [
                "table" => $this->table,
                "column" => "note",
                "alias" => "note",
                "function" => ["Helper", "htmlspecialchars_decode"],
            ],
            "background",
            [
                "table" => $this->table,
                "column" => 'create_time',
                "alias" => 'create_time',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => $this->table,
                "column" => 'update_time',
                "alias" => 'update_time',
                "function" => ["Helper", "time_to_data"]
            ]
        ];

        $get = $this->sedjm->get($data, $this->table);

        if(count($get) > 0) {
            $status = \ApiStatus::CORRECT;
        } else {
            $status = \ApiStatus::ERROR;
        }

        $output = new \Models\ApiModel($status, $get);
        return $output;
    }

    public function get_all_notepads_notes($notepad_id) {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("notepad_id", $notepad_id, "=");

        $data = [
            "note_id",
            "notepad_id",
            "author_id",
            "title",
            [
                "table" => $this->table,
                "column" => "note",
                "alias" => "note",
                "function" => ["Helper", "htmlspecialchars_decode"],
            ],
            "background",
            [
                "table" => $this->table,
                "column" => 'create_time',
                "alias" => 'create_time',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => $this->table,
                "column" => 'update_time',
                "alias" => 'update_time',
                "function" => ["Helper", "time_to_data"]
            ]
        ];

        $get = $this->sedjm->get($data, $this->table);

        if(count($get) > 0) {
            $status = \ApiStatus::CORRECT;
        } else {
            $status = \ApiStatus::ERROR;
        }

        $output = new \Models\ApiModel($status, $get);
        return $output;
    }

    public function get_by_id($id) {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("note_id", $id, "=");

        $data = [
            "note_id",
            "notepad_id",
            "author_id",
            "title",
            [
                "table" => $this->table,
                "column" => "note",
                "alias" => "note",
                "function" => ["Helper", "htmlspecialchars_decode"],
            ],
            [
                "table" => $this->table,
                "column" => "author_id",
                "alias" => "user_data",
                "function" => [$this, "get_user_data"],
            ],
            "background",
            [
                "table" => $this->table,
                "column" => 'create_time',
                "alias" => 'create_time',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => $this->table,
                "column" => 'update_time',
                "alias" => 'update_time',
                "function" => ["Helper", "time_to_data"]
            ]
        ];

        $get = $this->sedjm->get($data, $this->table);

        if(count($get) > 0) {
            $status = \ApiStatus::CORRECT;
        } else {
            $status = \ApiStatus::ERROR;
        }

        $output = new \Models\ApiModel($status, $get);
        return $output;
    }

    public function create(array $data) {

        if(
            !empty($data['notepad_id'])
            && !empty($data['author_id'])
        ) {
            $project_id = $this->get_project_by_notepads($data['notepad_id']);
            $project = new \Projects($data['author_id'], $project_id);

            if($project->auth_user_in_project()) {

                $notepad_id = $data['notepad_id'];
                $author_id = $data['author_id'];
                $title = !empty($data['title']) ? $data['title'] : "";
                $note = !empty($data['note']) ? $data['note'] : "";
                $background = !empty($data['background']) ? $data['background'] : null;

                $table = "Notes";

                $data = [
                    "notepad_id" => $notepad_id,
                    "author_id" => $author_id,
                    "title" => $title,
                    "note" => $note,
                    "background" => $background,
                    "create_time" => time(),
                    "update_time" => time()
                ];

                $insert = $this->sedjm->insert($data, $this->table);

                $output = new \Models\ApiModel(\ApiStatus::from($insert['status']), ["id" => $insert['id']]);

                return $output;

            } else {
                $output = new \Models\ApiModel(\ApiStatus::ERROR, null, ['Empty data']);
                return $output;
            }

        } else {

            $output = new \Models\ApiModel(\ApiStatus::ERROR, [], ['Unauthorize']);
            $output->set_code(401);

            return $output;
        }

    }

    public function update($id, array $data) {

        if(
            !empty($id)
        ) {
            $update_data = [];

            if(!empty($data['title']))
                $update_data["title"] = $data['title'];

            if(!empty($data['note']))
                $update_data["note"] = $data['note'];

            if(!empty($data['background']))
                $update_data["background"] = $data['background'];

            $update_data["update_time"] = time();

            $this->sedjm->clear_all();
            $this->sedjm->set_where("note_id", $id, "=");
            $insert = $this->sedjm->update($update_data, $this->table);

            $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
            return $output;

        } else {
            $output = new \Models\ApiModel(\ApiStatus::ERROR, null, ['Empty data']);
            return $output;
        }
    }

    public function delete($id) {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("note_id", $id, "=");
        $insert = $this->sedjm->delete($this->table);

        $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
        return $output;
    }

    // private

    public function get_project_by_notepads($notepads_id): ?int {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("notepad_id", $notepads_id, "=");
        $get = $this->sedjm->get(["project_id"], "Notepad");

        if(!empty($get))
            return $get[0]['project_id'];
        else
            return -1;
    }

    public static function get_user_data($user_id) {

        global $main;

        $table = "Users";
        $additionalTable = "UserData";

        $main->sedjm->clear_all();
        $main->sedjm->set_where("user_id", $user_id, "=");
        $get_data = $main->sedjm->get(["nick", "email"], $table);
        $additional_data = $main->sedjm->get(["field_key", "value"], $additionalTable);

        $additional = [];
        foreach($additional_data as $key => $value) {

            $additional[$value['field_key']] = $value['value'];

        }

        $output = [
            "data" => $get_data[0],
            "additional_data" => $additional
        ];

        return $output;
    }
}
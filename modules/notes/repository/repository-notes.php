<?php

namespace Notes\Repository;

interface RepositoryNotesInterface extends \RepositoryInterface
{
    public function get_all_notepads_notes(int $notepad_id);
}

class NotesRepository implements RepositoryNotesInterface
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "Notes";

    public function __construct()
    {
        global $main;
        $this->sedjm = $main->sedjm;
    }

    public function get_all()
    {

        $this->sedjm->clear_all();

        $data = [
            "notepad_id",
            "author_id",
            "title",
            "note",
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

    public function get_all_notepads_notes($notepad_id)
    {

        $this->sedjm->clear_all();
        $this->sedjm->set_where("notepad_id", $notepad_id, "=");

        $data = [
            "notepad_id",
            "author_id",
            "title",
            "note",
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
        $this->sedjm->set_where("note_id", $id, "=");

        $data = [
            "notepad_id",
            "author_id",
            "title",
            "note",
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
            !empty($data['notepad_id'])
            && !empty($data['author_id'])
            && !empty($data['title'])
        ) {
            $project_id = $this->get_project_by_notepads($data['notepad_id']);
            $project = new \Projects($data['author_id'], $project_id);

            if ($project->auth_user_in_project()) {

                $notepad_id = $data['notepad_id'];
                $author_id = $data['author_id'];
                $title = $data['title'];
                $note = $data['note'];

                $table = "Notes";

                $data = [
                    "notepad_id" => $notepad_id,
                    "author_id" => $author_id,
                    "title" => $title,
                    "note" => $note,
                    "create_time" => time(),
                    "update_time" => time()
                ];

                $insert = $this->sedjm->insert($data, $this->table);

                $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));

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

    public function update($id, array $data)
    {

        if (
            !empty($id)
            && !empty($data['title'])
        ) {
            $data = [
                "title" => $data['title'],
                "note" => $data['note'],
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
        $this->sedjm->set_where("note_id", $id, "=");
        $insert = $this->sedjm->delete($this->table);

        $output = new \Models\ApiModel(\ApiStatus::from($insert['status']));
        return $output;
    }

    // private

    public function get_project_by_notepads($notepads_id): ?int
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("notepad_id", $notepads_id, "=");
        $get = $this->sedjm->get(["project_id"], "Notepad");

        if (!empty($get))
            return $get[0]['project_id'];
        else
            return -1;
    }
}
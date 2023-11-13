<?php

namespace Notes\Controller;

class NotesApiController
{

    use \ModuleManager\LoadFile;
    private $repository;

    public function __construct()
    {

        $this->repository = new \Notes\Repository\NotesRepository();

        // NOTES 

        // get_notes

        $api = [
            "link" => 'get_notes',
            "function" => [$this, 'get_notes'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // get_note

        $api = [
            "link" => 'get_note',
            "function" => [$this, 'get_note'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_note

        $api = [
            "link" => 'add_note',
            "function" => [$this, 'add_note'],
            "http_methods" => "POST",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // update_note

        $api = [
            "link" => 'update_note',
            "function" => [$this, 'update_note'],
            "http_methods" => "PUT",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // delete_note

        $api = [
            "link" => 'delete_note',
            "function" => [$this, 'delete_note'],
            "http_methods" => "DELETE",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);


    }

    // Notes

    /**
     * function to add new notes
     * @param int $notepad_id
     * @param int $author_id
     * @param string $title
     * @param array $note
     * @return \Models\ApiModel $output
     */
    public function add_note($input): \Models\ApiModel
    {
        $data = [
            "notepad_id" => $input['notepad_id'],
            "author_id" => $input['user_id'],
            "title" => $input['title'],
            "note" => $input['note'],
            "background" => $input['background'],
            "project_id" => $input["project_id"]
        ];

        return $this->repository->create($data);
    }

    /**
     * function to update notes
     * @param int $note_id
     * @param string $title
     * @param array $note
     * @return array
     */
    public function update_note($input): \Models\ApiModel
    {

        $data = [];

        if (!empty($input['title']))
            $data["title"] = $input['title'];

        if (!empty($input['note']))
            $data["note"] = $input['note'];


        return $this->repository->update($input['note_id'], $data);
    }


    public function get_notes($input): \Models\ApiModel
    {
        return $this->repository->get_all_notepads_notes($input['notepad_id']);
    }

    public function get_note($input): \Models\ApiModel
    {
        return $this->repository->get_by_id($input['note_id']);
    }

    public function delete_note($input): \Models\ApiModel
    {
        return $this->repository->delete($input['note_id']);
    }

}

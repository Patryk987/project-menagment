<?php

namespace Notes\Controller;

class NotepadsApiController
{

    use \ModuleManager\LoadFile;
    private $repository;

    public function __construct()
    {
        $this->repository = new \Notes\Repository\NotepadRepository();
        // get_notepads

        $api = [
            "link" => 'get_notepads',
            "function" => [$this, 'get_notepads'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_notepad

        $api = [
            "link" => 'add_notepad',
            "function" => [$this, 'add_notepad'],
            "http_methods" => "POST",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // update_notepad

        $api = [
            "link" => 'update_notepad',
            "function" => [$this, 'update_notepad'],
            "http_methods" => "PUT",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);


    }

    public function get_notepads($input)
    {

    }

    public function add_notepad($input)
    {
        return $this->repository->create($input);
    }

    public function update_notepad($input)
    {

    }

}

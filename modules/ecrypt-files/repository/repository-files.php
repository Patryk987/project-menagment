<?php

namespace Files\Repository;


class FilesRepository implements \RepositoryInterface
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "";

    public function __construct()
    {
        global $main;
        $this->sedjm = $main->sedjm;
    }

    public function get_all()
    {

    }
    public function get_by_id($id)
    {

    }
    public function create(array $data)
    {


    }
    public function update($id, array $data)
    {

    }
    public function delete($id)
    {

    }
}
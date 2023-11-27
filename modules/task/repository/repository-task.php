<?php

namespace Tasks\Repository;


class TasksRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private $table = "";
    private int $project_id;
    public function __construct($project_id)
    {
        global $main;
        $this->project_id = $project_id;
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
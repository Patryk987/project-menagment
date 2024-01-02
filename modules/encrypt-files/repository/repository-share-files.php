<?php

namespace Files\Repository;


class FilesRepository
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

}
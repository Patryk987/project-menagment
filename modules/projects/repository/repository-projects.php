<?php

namespace Projects;

class ProjectsRepository
{
    public $ftp_table = 'FTP';
    public $project_table = 'Projects';

    public function get_by_id($id)
    {
        global $main;
        $main->sedjm->clear_all();
        $main->sedjm->set_join(
            'LEFT',
            [
                'table' => $this->ftp_table,
                'column' => 'project_id'
            ],
            [
                'table' => $this->project_table,
                'column' => 'project_id'
            ],
        );


        $data = [
            'project_id',
            'name',
            'description',
            'photo_url',
            'status',
            'create_time',
            [
                "column" => "serwer",
                "alias" => "serwer",
                "table" => $this->ftp_table
            ],
            [
                "column" => "user",
                "alias" => "user",
                "table" => $this->ftp_table
            ],
            [
                "column" => "password",
                "alias" => "password",
                "table" => $this->ftp_table
            ],
            [
                "column" => "port",
                "alias" => "port",
                "table" => $this->ftp_table
            ]
        ];


        $main->sedjm->set_where("project_id", $id, '=');
        $results = $main->sedjm->get($data, $this->project_table);
        $main->sedjm->clear_all();

        if (!empty($results)) {
            return $results[0];
        } else {
            return [];
        }
    }

    public function get_all()
    {
    }

    public function get_project_list_by_id($user_id)
    {
        return $this->get_project_list($user_id);
    }

    public function create($data): bool
    {
        $project = $this->create_new_project($data);
        if ($project->get_status()) {
            $this->create_new_ftp_connect($data, $project->get_id());
            return true;
        }

        return false;
    }

    public function update($id, $data)
    {
        $this->update_project($id, $data);
        $this->update_ftp_connect($id, $data);

    }

    public function delete_by_id($id)
    {
    }

    // Private 

    private function sava_photo($user_id, $photo)
    {
        $file_upload = new \ModuleManager\FileUpload\FileUploader(["png", "jpg"]);
        $file_upload = $file_upload->upload_file($user_id, $photo, "");
        return $file_upload['file_path'];
    }

    private function create_new_project($data): Model\InsertModel
    {
        global $main;

        $data = [
            'owner_id' => $data['owner_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'photo_url' => $this->sava_photo($data['owner_id'], $data['photo']),
            'status' => 1,
            'create_time' => time(),
        ];

        $results = $main->sedjm->insert($data, $this->project_table);

        $output = new Model\InsertModel($results['status']);
        if ($results['status']) {
            $output->set_id($results['id']);
        }

        return $output;
    }

    private function update_project($id, $input): Model\InsertModel
    {
        global $main;
        $data = [];

        if (!empty($input['name']))
            $data['name'] = $input['name'];

        if (!empty($input['description']))
            $data['description'] = $input['description'];

        if (!empty($input['photo'])) {
            $photo_url = $this->sava_photo($input['owner_id'], $input['photo']);
            $data['photo_url'] = $photo_url;
        }
        if (!empty($input['status']))
            $data['status'] = $input['status'];

        $main->sedjm->clear_all();
        $main->sedjm->set_where("project_id", $id, '=');
        $results = $main->sedjm->update($data, $this->project_table);

        $output = new Model\InsertModel($results['status']);
        return $output;
    }

    private function create_new_ftp_connect($data, $project_id): Model\InsertModel
    {
        global $main;

        $data = [
            'project_id' => $project_id,
            'serwer' => $data['serwer'],
            'user' => $data['user'],
            'password' => $data['password'],
            'port' => $data['port']
        ];

        $results = $main->sedjm->insert($data, $this->ftp_table);

        $output = new Model\InsertModel($results['status']);
        if ($results['status']) {
            $output->set_id($results['id']);
        }

        return $output;
    }

    private function update_ftp_connect($project_id, $input): Model\InsertModel
    {
        global $main;

        $data = [];

        if (!empty($input['serwer']))
            $data['serwer'] = $input['serwer'];

        if (!empty($input['user']))
            $data['user'] = $input['user'];

        if (!empty($input['password']))
            $data['password'] = $input['password'];

        if (!empty($input['port']))
            $data['port'] = $input['port'];

        if (!empty($data)) {
            $main->sedjm->clear_all();
            $main->sedjm->set_where("project_id", $project_id, '=');
            $results = $main->sedjm->update($data, $this->ftp_table);

            $output = new Model\InsertModel($results['status']);

        } else {
            $output = new Model\InsertModel(false);

        }
        return $output;
    }

    private function create_collaborator($data, $project_id): Model\InsertModel
    {
        global $main;

        $data = [
            'project_id' => $project_id,
            'user_id' => $data['user_id'],
            'role' => $data['role'],
            'status' => 1,
            'joining_date' => time()
        ];

        $results = $main->sedjm->insert($data, "Collaborators");

        $output = new Model\InsertModel($results['status']);
        if ($results['status']) {
            $output->set_id($results['id']);
        }

        return $output;
    }

    private function get_project_list($owner_id): array
    {
        global $main;

        $data = [
            'project_id',
            'name',
            'description',
            'photo_url',
            'status',
            'create_time'
        ];

        // TODO: ADD COLLABORATORS

        $main->sedjm->clear_all();
        $main->sedjm->set_where("owner_id", $owner_id, '=');
        $main->sedjm->set_where("status", 1, '=');
        $results = $main->sedjm->get($data, $this->project_table);
        $main->sedjm->clear_all();

        return $results;
    }

}
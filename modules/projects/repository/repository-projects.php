<?php

namespace Projects;

class ProjectsRepository
{
    public function get_by_id($id)
    {
    }

    public function get_all()
    {
    }

    public function get_project_list_by_id($user_id)
    {
        return $this->get_project_list($user_id);
    }

    public function create($data)
    {
        $project = $this->create_new_project($data);
        if ($project->get_status()) {
            $this->create_new_ftp_connect($data, $project->get_id());
            // $this->create_collaborator($data, $project->get_id());
        }
    }

    public function update($id, $data)
    {
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

        $results = $main->sedjm->insert($data, "Projects");

        $output = new Model\InsertModel($results['status']);
        if ($results['status']) {
            $output->set_id($results['id']);
        }

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

        $results = $main->sedjm->insert($data, "FTP");

        $output = new Model\InsertModel($results['status']);
        if ($results['status']) {
            $output->set_id($results['id']);
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
        $results = $main->sedjm->get($data, "Projects");
        $main->sedjm->clear_all();

        return $results;
    }
}
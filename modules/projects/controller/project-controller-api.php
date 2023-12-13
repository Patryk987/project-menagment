<?php

namespace Projects\Controller;

use Projects\ProjectsRepository;

class ProjectsApi
{
    public string $table = "Collaborators";
    use \ModuleManager\LoadFile;

    public function init_api()
    {

        \ModuleManager\Pages::set_endpoint([
            "link" => 'find_user',
            "function" => [$this, 'find_user'],
            "http_methods" => 'GET',
            "permission" => [1, 2, 3, 11]
        ]);

        \ModuleManager\Pages::set_endpoint([
            "link" => 'add_collaborators',
            "function" => [$this, 'add_collaborators'],
            "http_methods" => 'POST',
            "permission" => [1, 2, 3, 11]
        ]);


    }

    public function find_user($input): \Models\ApiModel
    {
        global $main;

        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (!empty($input['search_nick'])) {
            $main->sedjm->clear_all();
            $main->sedjm->set_limit(25);
            $main->sedjm->set_where("nick", "%" . $input['search_nick'] . "%", "LIKE", "OR");
            $main->sedjm->set_where("email", "%" . $input['search_nick'] . "%", "LIKE", "OR");
            $results = $main->sedjm->get(["user_id", "nick", "email"], "Users");
            $output->set_message($results);
            $output->set_status(\ApiStatus::CORRECT);
        }
        return $output;

    }

    public function add_collaborators($input): \Models\ApiModel
    {
        global $main;

        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (empty($input['collaborators_id'])) {
            $output->set_error(["no user selected"]);
            return $output;
        }

        if (empty($input['project_id'])) {
            $output->set_error(["no project selected"]);
            return $output;
        }

        $main->sedjm->clear_all();
        $main->sedjm->set_where("user_id", $input['collaborators_id'], "=");
        $main->sedjm->set_where("project_id", $input['project_id'], "=");
        $collaborators = $main->sedjm->get(["collaborator_id"], $this->table);
        if (empty($collaborators)) {
            $data = [
                "project_id" => $input['project_id'],
                "user_id" => $input['collaborators_id'],
                "role" => \CollaboratorsRole::COLLABORATORS->value,
                "status" => \CollaboratorsStatus::ACTIVE->value
            ];
            $insert = $main->sedjm->insert($data, $this->table);

            if ($insert['status']) {
                $output->set_status(\ApiStatus::from($insert['status']));
                $output->set_message(["id" => $insert['id']]);
            }
        }

        return $output;

    }


}

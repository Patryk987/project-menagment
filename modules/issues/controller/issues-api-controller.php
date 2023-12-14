<?php

namespace Issues\Controller;

use Issues\Repository\IssuesRepository;

class IssuesApiController
{

    use \ModuleManager\LoadFile;
    private $repository;

    public function __construct()
    {

        // get_issues

        $api = [
            "link" => 'get_issues',
            "function" => [$this, 'get_issues'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_issues

        $api = [
            "link" => 'add_issues',
            "function" => [$this, 'add_issues'],
            "http_methods" => "POST",
            "permission" => [0]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // update_issues 

        $api = [
            "link" => 'update_issues',
            "function" => [$this, 'update_issues'],
            "http_methods" => "PUT",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);


    }

    public function get_issues($input)
    {

        $output = new \Models\ApiModel(\ApiStatus::ERROR);

        if (!empty($input['project_id'])) {

            $project_id = $input['project_id'];
            $repository = new IssuesRepository($project_id);
            $data = $repository->get_all();

            if ($data->get_status() == \ApiStatus::CORRECT) {
                $output->set_message($data->get_message());
                $output->set_status($data->get_status());
            }

        }

        return $output;
    }

    public function add_issues($input)
    {

        $output = new \Models\ApiModel(\ApiStatus::ERROR);

        if (
            !empty($input['project_id'])
            && !empty($input['title'])
        ) {

            $project_id = $input['project_id'];
            $repository = new IssuesRepository($project_id);

            $data = [
                "project_id" => $input['project_id'],
                "author_id" => !empty($input['user_id']) ? $input['user_id'] : null,
                "title" => $input['title'],
                "description" => !empty($input['description']) ? $input['description'] : null,
            ];

            $response = $repository->create($data);
            $output->set_message($response->get_message());
            $output->set_status($response->get_status());

        }

        return $output;
    }

    public function update_issues($input)
    {

        $output = new \Models\ApiModel(\ApiStatus::ERROR);

        if (
            !empty($input['project_id'])
            && !empty($input['issues_id'])
        ) {

            $project_id = $input['project_id'];
            $repository = new IssuesRepository($project_id);

            $update_data = [];

            if (!empty($input["title"]))
                $update_data["title"] = $input["title"];

            if (!empty($input["description"]))
                $update_data["description"] = $input["description"];

            if (!empty($input["status"]))
                $update_data["status"] = $input["status"];

            $response = $repository->update($input['issues_id'], $update_data);
            $output->set_status($response->get_status());

        }

        return $output;

    }

}

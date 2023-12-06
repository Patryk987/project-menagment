<?php

namespace Tasks\Controller;

use Tasks\Repository as Repository;
use ModuleManager\Forms\Forms;
use Tasks\Enums as Enums;

class TasksGroupApiController
{

    public function __construct()
    {

        // NOTES 

        // get_task_group

        $api = [
            "link" => 'get_task_group',
            "function" => [$this, 'get_task_group'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_task_group

        $api = [
            "link" => 'add_task_group',
            "function" => [$this, 'add_task_group'],
            "http_methods" => "POST",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);


    }

    public function get_task_group($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (!empty($input['project_id'])) {

            try {

                $repository = new Repository\TasksGroupRepository($input['project_id']);

                $data = $repository->get_all();

                if (!empty($data)) {
                    $output->set_status(\ApiStatus::CORRECT);
                    $output->set_message($data);
                }

            } catch (\Throwable $th) {

                $this->set_error($th);

            }


        }


        return $output;
    }

    public function add_task_group($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['name'])
        ) {

            try {

                $repository = new Repository\TasksGroupRepository($input['project_id']);

                $data = [
                    "author_id" => $input['user_id'],
                    "group_name" => $input['name'],
                    "group_description" => !empty($input['description']) ? $input['description'] : null,
                    "color" => !empty($input['color']) ? $input['color'] : null
                ];
                $insert = $repository->create($data);

                if ($insert['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                    $output->set_message(['id' => $insert['id']]);
                }

            } catch (\Throwable $th) {

                $this->set_error($th);

            }


        }


        return $output;
    }

    private function set_error($th)
    {
        $details = [
            "message" => $th->getMessage(),
            "code" => $th->getCode(),
            "file" => $th->getFile(),
            "line" => $th->getLine()
        ];

        \ModuleManager\Main::set_error('Taks API', 'ERROR', $details);
    }

}

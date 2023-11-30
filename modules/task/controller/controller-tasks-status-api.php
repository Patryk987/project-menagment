<?php

namespace Tasks\Controller;

use Tasks\Repository as Repository;
use ModuleManager\Forms\Forms;
use Tasks\Enums as Enums;

class TasksStatusApiController
{

    public function __construct()
    {

        // get_task_status

        $api = [
            "link" => 'get_task_status',
            "function" => [$this, 'get_task_status'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_task_status

        $api = [
            "link" => 'add_task_status',
            "function" => [$this, 'add_task_status'],
            "http_methods" => "POST",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // update_task_status

        $api = [
            "link" => 'update_task_status',
            "function" => [$this, 'update_task_status'],
            "http_methods" => "PUT",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // delete_task_status

        $api = [
            "link" => 'delete_task_status',
            "function" => [$this, 'delete_task_status'],
            "http_methods" => "DELETE",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);


    }

    public function get_task_status($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
        ) {

            try {

                $repository = new Repository\TasksStatusRepository($input['project_id'], $input['task_group_id']);

                $data = $repository->get_all();

                $output->set_status(\ApiStatus::CORRECT);
                $output->set_message($data);

            } catch (\Throwable $th) {

                $this->set_error($th);

            }


        }


        return $output;
    }

    public function add_task_status($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['name'])
        ) {

            try {

                $repository = new Repository\TasksStatusRepository($input['project_id'], $input['task_group_id']);

                $input_data = [
                    "name" => $input['name'],
                    "color" => !empty($input['color']) ? $input['color'] : ""
                ];

                $data = $repository->create($input_data);

                if ($data['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                    $output->set_message(["id" => $data['id']]);
                }
            } catch (\Throwable $th) {

                $this->set_error($th);

            }


        }


        return $output;
    }

    public function update_task_status($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['task_status_id'])
        ) {

            try {

                $repository = new Repository\TasksStatusRepository($input['project_id'], $input['task_group_id']);

                $input_data = [];

                if (!empty($input['name']))
                    $input_data["name"] = $input['name'];

                if (!empty($input['color']))
                    $input_data["color"] = $input['color'];

                $data = $repository->update($input['task_status_id'], $input_data);

                if ($data['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                    // $output->set_message($data);
                }

            } catch (\Throwable $th) {

                $this->set_error($th);

            }


        }


        return $output;
    }

    public function delete_task_status($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['task_status_id'])
        ) {

            try {

                $repository = new Repository\TasksStatusRepository($input['project_id'], $input['task_group_id']);

                $data = $repository->delete($input['task_status_id']);

                if ($data['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                    // $output->set_message($data);

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

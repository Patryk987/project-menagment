<?php

namespace Tasks\Controller;

use Tasks\Repository as Repository;
use ModuleManager\Forms\Forms;
use Tasks\Enums as Enums;

class TasksApiController
{

    public function __construct()
    {

        // NOTES 

        // get_task

        $api = [
            "link" => 'get_task',
            "function" => [$this, 'get_task'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // get_task_details

        $api = [
            "link" => 'get_task_details',
            "function" => [$this, 'get_task_details'],
            "http_methods" => "GET",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // add_task

        $api = [
            "link" => 'add_task',
            "function" => [$this, 'add_task'],
            "http_methods" => "POST",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // update_task

        $api = [
            "link" => 'update_task',
            "function" => [$this, 'update_task'],
            "http_methods" => "PUT",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

        // delete_task

        $api = [
            "link" => 'delete_task',
            "function" => [$this, 'delete_task'],
            "http_methods" => "DELETE",
            "permission" => [1, 2, 11]
        ];

        \ModuleManager\Pages::set_endpoint($api);

    }

    public function get_task_details($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['task_id'])
        ) {
            try {

                $repository = new Repository\TasksRepository($input['project_id'], $input['task_group_id']);

                $data = $repository->get_by_id($input['task_id']);

                $output->set_status(\ApiStatus::CORRECT);
                $output->set_message($data);


            } catch (\Throwable $th) {

                $this->set_error($th);

            }

        } else {
            $output->set_error(["add all data"]);
        }

        return $output;
    }

    public function get_task($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
        ) {
            try {

                $repository = new Repository\TasksRepository($input['project_id'], $input['task_group_id']);

                $data = $repository->get_all();

                $output->set_status(\ApiStatus::CORRECT);
                $output->set_message($data);


            } catch (\Throwable $th) {

                $this->set_error($th);

            }

        }

        return $output;
    }

    public function add_task($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['user_id'])
            && !empty($input['task'])
        ) {
            try {

                $repository = new Repository\TasksRepository($input['project_id'], $input['task_group_id']);

                $data = [
                    "author_id" => $input["user_id"],
                    "task_name" => $input["task"],
                    "content" => !empty($input["content"]) ? $input["content"] : null,
                    "end_time" => !empty($input["end_time"]) ? $input["end_time"] : null,
                    "repeat_status" => !empty($input["repeat_status"]) ? $input["repeat_status"] : null,
                    "color" => !empty($input["color"]) ? $input["color"] : null,
                ];

                $insert = $repository->create($data);

                if ($insert['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                    $output->set_message(["id" => $insert['id']]);
                }

            } catch (\Throwable $th) {

                $this->set_error($th);

            }

        } else {
            $output->set_error(["add all data"]);
        }


        return $output;
    }

    public function update_task($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['task_id'])
        ) {
            try {

                $repository = new Repository\TasksRepository($input['project_id'], $input['task_group_id']);

                $data = [];
                if (!empty($input['name']))
                    $data["task_name"] = $input['name'];

                if (!empty($input['content']))
                    $data["content"] = $input['content'];

                if (!empty($input['end_time']))
                    $data["end_time"] = $input['end_time'];

                if (!empty($input['repeat_status']))
                    $data["repeat_status"] = $input['repeat_status'];

                if (!empty($input['color']))
                    $data["color"] = $input['color'];

                if (!empty($input['task_status']))
                    $data["task_status_id"] = $input['task_status'];

                $update = $repository->update($input['task_id'], $data);


                if ($update['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
                }

            } catch (\Throwable $th) {

                $this->set_error($th);

            }

        }


        return $output;
    }

    public function delete_task($input): \Models\ApiModel
    {
        $output = new \Models\ApiModel(\ApiStatus::ERROR);
        if (
            !empty($input['project_id'])
            && !empty($input['task_group_id'])
            && !empty($input['task_id'])
        ) {
            try {

                $repository = new Repository\TasksRepository($input['project_id'], $input['task_group_id']);

                $delete = $repository->delete($input['task_id']);
                if ($delete['status']) {
                    $output->set_status(\ApiStatus::CORRECT);
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

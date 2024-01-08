<?php

namespace Files\Repository;


class FilesShareRepository extends FilesRepository
{

    public string $files_share_table = "ShareFile";
    protected string $share_folder = "/share";


    public function share_file($file, $filename, $type, $recipient_email, $days_live = 7): \Models\ApiModel
    {
        $return = new \Models\ApiModel(\ApiStatus::ERROR);
        $recipient_data = $this->verify_user_email($recipient_email);
        if ($recipient_data['status']) {

            if (
                !empty($file)
                && !empty($filename)
                && !empty($type)
                && !empty($recipient_email)
                && !empty($days_live)
            ) {

                if ($this->connect_to_ftp()) {
                    $this->check_share_folder_if_not_exist_create_him();
                    $file_upload_response = $this->upload_file($file, $this->share_folder, $filename, $type, $recipient_data["data"]["user_id"]);
                    $this->save_share_folder_data($recipient_email, $file_upload_response['id'], $recipient_data["data"]["user_id"], $days_live);
                    $return->set_status(\ApiStatus::CORRECT);
                }

            } else {
                $return->set_error(["empty_data"]);
            }

        } else {
            $return->set_error(["incorrect_email"]);
        }


        return $return;
    }

    private function check_share_folder_if_not_exist_create_him()
    {
        if (!$this->ftp->check_folder($this->share_folder)) {
            $this->ftp->create_catalogue($this->share_folder);
        }
    }

    private function save_share_folder_data($recipient_email, $file_id, $user_id, $day = 7)
    {
        $life_time = $day * 86400;
        $this->sedjm->clear_all();
        $data = [
            "file_id" => $file_id,
            "recipient_email" => $recipient_email,
            "upload_time" => time(),
            "life_time" => $life_time,
            "user_id" => $user_id,
            "protection_level" => "",
        ];
        $results = $this->sedjm->insert($data, $this->files_share_table);
        return $results;
    }

    public function delete_share_file($id): bool
    {

        $file = $this->get_share_files_by_id($id);
        if ($file['status']) {
            try {
                $this->delete_file($file['data']['encrypt_file_name'], $file['data']['directory']);
            } catch (\Throwable $th) {
                echo $th->__toString();
            }
            $this->sedjm->clear_all();
            $this->sedjm->set_where("share_file_id", $id, "=");
            $delete_results = $this->sedjm->delete($this->files_share_table);
            return $delete_results['status'];
        } else {
            return false;
        }

    }

    public function list_share_files()
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $this->sedjm->set_join(
            'RIGHT',
            [
                'table' => $this->files_share_table,
                'column' => 'file_id'
            ],
            [
                'table' => $this->files_table,
                'column' => 'file_id'
            ],
        );

        $data = [

            [
                "column" => "share_file_id",
                "alias" => "share_file_id",
                "table" => $this->files_share_table
            ],
            [
                "column" => "file_id",
                "alias" => "file_id",
                "table" => $this->files_share_table
            ],
            [
                "column" => "recipient_email",
                "alias" => "recipient_email",
                "table" => $this->files_share_table
            ],
            [
                "column" => "upload_time",
                "alias" => "upload_time",
                "table" => $this->files_share_table
            ],
            [
                "column" => "life_time",
                "alias" => "life_time",
                "table" => $this->files_share_table
            ],
            [
                "column" => "protection_level",
                "alias" => "protection_level",
                "table" => $this->files_share_table
            ],
            [
                "column" => "file_name",
                "alias" => "file_name",
                "table" => $this->files_table
            ],
            [
                "column" => "encrypt_file_name",
                "alias" => "encrypt_file_name",
                "table" => $this->files_table
            ],
        ];

        $results = $this->sedjm->get($data, $this->files_table);
        return $results;
    }

    public function get_share_files_by_id($id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("share_file_id", $id, "=");
        $this->sedjm->set_join(
            'LEFT',
            [
                'table' => $this->files_table,
                'column' => 'file_id'
            ],
            [
                'table' => $this->files_share_table,
                'column' => 'file_id'
            ],
        );

        $data = [
            "share_file_id",
            "file_id",
            "recipient_email",
            "upload_time",
            "life_time",
            "protection_level",
            [
                "column" => "file_name",
                "alias" => "file_name",
                "table" => $this->files_table
            ],
            [
                "column" => "encrypt_file_name",
                "alias" => "encrypt_file_name",
                "table" => $this->files_table
            ],
            [
                "column" => "directory",
                "alias" => "directory",
                "table" => $this->files_table
            ],
        ];

        $results = $this->sedjm->get($data, $this->files_share_table);
        if (!empty($results)) {
            $output = [
                "status" => true,
                "data" => $results[0]
            ];
            return $output;
        }

        return ["status" => false];
    }

    public function get_user_shared_file($user_id)
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("user_id", $user_id, "=");
        $this->sedjm->set_join(
            'RIGHT',
            [
                'table' => $this->files_table,
                'column' => 'file_id'
            ],
            [
                'table' => $this->files_share_table,
                'column' => 'file_id'
            ],
        );

        $data = [
            [
                "column" => "share_file_id",
                "alias" => "share_file_id",
                "table" => $this->files_share_table
            ],
            [
                "column" => "file_id",
                "alias" => "file_id",
                "table" => $this->files_share_table
            ],
            [
                "column" => "recipient_email",
                "alias" => "recipient_email",
                "table" => $this->files_share_table
            ],
            [
                "column" => "upload_time",
                "alias" => "upload_time",
                "table" => $this->files_share_table
            ],
            [
                "column" => "life_time",
                "alias" => "life_time",
                "table" => $this->files_share_table
            ],
            [
                "column" => "protection_level",
                "alias" => "protection_level",
                "table" => $this->files_share_table
            ],
            [
                "column" => "file_name",
                "alias" => "file_name",
                "table" => $this->files_table
            ],
            [
                "column" => "encrypt_file_name",
                "alias" => "encrypt_file_name",
                "table" => $this->files_table
            ],
        ];

        $results = $this->sedjm->get($data, $this->files_share_table);

        return $results;

    }

    private function verify_user_email($mail): array
    {
        $output = ["status" => false];
        $this->sedjm->clear_all();
        $this->sedjm->set_where("email", $mail, "=");
        $results = $this->sedjm->get(["user_id", "nick", "email"], "Users");
        if (!empty($results)) {
            $output["status"] = true;
            $output["data"] = $results[0];
        }
        return $output;
    }
}
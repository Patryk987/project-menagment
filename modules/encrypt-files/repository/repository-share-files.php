<?php

namespace Files\Repository;


class FilesShareRepository extends FilesRepository
{

    public string $files_share_table = "ShareFile";
    protected string $share_folder = "/share";


    public function share_file($file, $filename, $type, $recipient_email, $days_live = 7)
    {
        if (
            !empty($file)
            && !empty($filename)
            && !empty($type)
            && !empty($recipient_email)
            && !empty($days_live)
        ) {

            if ($this->connect_to_ftp()) {
                $this->check_share_folder_if_not_exist_create_him();
                $file_upload_response = $this->upload_file($file, $this->share_folder, $filename, $type);
                $this->save_share_folder_data($recipient_email, $file_upload_response['id'], $days_live);
            }

        } else {

        }
    }

    private function check_share_folder_if_not_exist_create_him()
    {
        if (!$this->ftp->check_folder($this->share_folder)) {
            $this->ftp->create_catalogue($this->share_folder);
        }
    }

    private function save_share_folder_data($recipient_email, $file_id, $day = 7)
    {
        $life_time = $day * 86400;
        $this->sedjm->clear_all();
        $data = [
            "file_id" => $file_id,
            "recipient_email" => $recipient_email,
            "upload_time" => time(),
            "life_time" => $life_time,
            "protection_level" => "",
        ];
        $results = $this->sedjm->insert($data, $this->files_share_table);
        return $results;
    }

    public function list_share_files()
    {
        $this->sedjm->clear_all();
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
        ];

        $results = $this->sedjm->get($data, $this->files_share_table);
        return $results;
    }
}
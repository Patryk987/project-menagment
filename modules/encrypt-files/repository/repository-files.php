<?php

namespace Files\Repository;


class FilesRepository
{

    private \ModuleManager\SEDJM $sedjm;
    private \FTP $ftp;
    private $table = "";
    private int $project_id;
    public $ftp_connect_data_table = "FTP";
    public $files_table = "RemoteFiles";
    public $connect_data;

    public function __construct($project_id)
    {
        global $main;
        $this->project_id = $project_id;
        $this->sedjm = $main->sedjm;

        $this->connect_data = $this->get_ftp_connect_data();

    }

    public function connect_to_ftp()
    {
        try {

            $this->ftp = new \FTP(
                $this->connect_data["serwer"],
                $this->connect_data["port"],
                $this->connect_data["user"],
                $this->connect_data["password"]
            );

            return true;

        } catch (\Throwable $th) {
            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            \ModuleManager\Main::set_error('FTP connect', 'ERROR', $details);

            return false;
        }
    }

    public function list_file($directory = ".")
    {
        $ftp_file = $this->ftp->list_files($directory);
        $x = $this->concat_files($ftp_file);

        return $x;
    }

    public function list_file_tree()
    {

    }

    public function download_file($file, $name)
    {

        $source = __DIR__ . "/" . $name;
        $data = $this->get_file_info($name);
        if ($data['status']) {

            $key = hex2bin($data["data"]['access_key']);
            $local_file = $data["data"]["file_name"];
            $this->ftp->get_file($source, $file);
            decryptFile($source, $key, __DIR__ . "/" . $local_file);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename(__DIR__ . "/" . $local_file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(__DIR__ . "/" . $local_file));
            flush();
            readfile(__DIR__ . "/" . $local_file);
            unlink(__DIR__ . "/" . $local_file);
            unlink($source);
            die();
        } else {
            $this->ftp->get_file($source, $file);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($source));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($source));
            flush();
            readfile($source);

            unlink($source);
        }
    }

    public function upload_file($file, $destination, $filename, $type)
    {
        $checksum = $this->create_checksum($file);
        $key = $this->create_key();
        var_dump(bin2hex($key));
        $encrypt_name = $this->create_encrypt_name();
        $this->save_file_info($encrypt_name, $key, $destination, $checksum, $filename, $type);

        encryptFile($file, $key, __DIR__ . "/" . $encrypt_name);

        $fp = fopen(__DIR__ . "/" . $encrypt_name, 'r');
        $this->ftp->send_file($fp, $destination, $encrypt_name);
        unlink(__DIR__ . "/" . $encrypt_name);
    }

    public function delete_file($id)
    {

    }

    private function get_ftp_connect_data()
    {
        $this->sedjm->clear_all();
        $this->sedjm->set_where("project_id", $this->project_id, "=");
        $data = $this->sedjm->get([
            "ftp_id",
            "project_id",
            "serwer",
            "user",
            "password",
            "port"
        ], $this->ftp_connect_data_table);

        return $data[0];
    }

    private function concat_files($file)
    {
        $output = [];

        foreach ($file as $key => $value) {
            if ($value["name"] != ".") {

                $encrypted_file_data = $this->get_file_info($value["name"]);

                $output[] = [
                    "pwd" => $value["pwd"],
                    "type" => $value["type"],
                    "permission" => $value["permission"],
                    "name" => $value["name"],
                    "display_name" => $encrypted_file_data['status'] ? $encrypted_file_data["data"]["file_name"] : $value["name"],
                    "size" => $value["size"],
                    "modify_time" => $value["modify_time"],
                    "encrypted" => $encrypted_file_data['status'] ? true : false,
                ];

            }
        }
        return $output;
        // 
    }

    private function save_file_info($encrypt_file_name, $key, $destination, $checksum, $filename, $type)
    {

        $data = [
            "user_id" => \ModuleManager\Main::$token['payload']->user_id,
            "ftp_id" => $this->connect_data["ftp_id"],
            "file_name" => $filename,
            "encrypt_file_name" => $encrypt_file_name,
            "file_extension" => $type,
            "directory" => $destination,
            "access_key" => bin2hex($key),
            "create_time" => time(),
            "checksum" => $checksum
        ];
        $this->sedjm->insert($data, $this->files_table);
    }

    private function get_file_info($encrypt_file_name)
    {
        $data = [
            "user_id",
            "ftp_id",
            "file_name",
            "encrypt_file_name",
            "file_extension",
            "directory",
            "access_key",
            "create_time",
            "checksum"
        ];
        $this->sedjm->clear_all();
        $this->sedjm->set_where("encrypt_file_name", $encrypt_file_name, "=");
        $data = $this->sedjm->get($data, $this->files_table);
        if (!empty($data)) {

            return ["status" => true, "data" => $data[0]];
        } else {
            return ["status" => false];

        }
    }

    private function create_key(): string
    {
        return random_bytes(32);
    }

    private function create_checksum($path): string
    {
        $fp = fopen($path, 'r');
        $buffer = fgets($fp, 4096);
        $checksum = crc32($buffer);
        return $checksum;
    }

    private function create_encrypt_name(): string
    {
        // TODO: IMPROVE
        return md5(rand() . rand() . time());
    }
}
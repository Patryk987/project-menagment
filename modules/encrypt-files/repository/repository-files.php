<?php

namespace Files\Repository;


class FilesRepository
{

    protected \ModuleManager\SEDJM $sedjm;
    protected \AES\EncryptDecryptFile $aes_file;
    protected \RSA\EncryptDecryptRSA $rsa_encrypt_decrypt;
    protected \FTP $ftp;
    private $table = "";
    protected int $project_id;
    public $ftp_connect_data_table = "FTP";
    public $files_table = "RemoteFiles";
    public $connect_data;

    public function __construct($project_id = null)
    {
        global $main;
        $this->sedjm = $main->sedjm;

        if (!empty($project_id)) {
            $this->innit_ftp_connect($project_id);
        }

        $this->aes_file = new \AES\EncryptDecryptFile();

    }

    public function innit_ftp_connect($project_id)
    {
        $this->project_id = $project_id;
        $this->connect_data = $this->get_ftp_connect_data();
        $this->rsa_encrypt_decrypt = new \RSA\EncryptDecryptRSA(
            $project_id,
            "project_",
            \ModuleManager\LocalStorage::get_data("project_" . $project_id . "_password", 'session', true)
        );
    }

    public function set_user_key($user_id, $key)
    {
        $this->rsa_encrypt_decrypt = new \RSA\EncryptDecryptRSA(
            $user_id,
            "user_",
            $key
        );
    }

    public function connect_to_ftp(): bool
    {
        try {
            if (!empty($this->connect_data)) {

                $this->ftp = new \FTP(
                    $this->connect_data["serwer"],
                    $this->connect_data["port"],
                    $this->connect_data["user"],
                    $this->connect_data["password"]
                );

                return true;

            }

        } catch (\Throwable $th) {
            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            \ModuleManager\Main::set_error('FTP connect', 'ERROR', $details);

        }

        return false;
    }

    public function list_file($directory = "."): array
    {
        $ftp_file = $this->ftp->list_files($directory);
        $concat_files = $this->concat_files($ftp_file);

        return $concat_files;
    }

    public function list_file_tree()
    {

    }

    public function download_file($file, $name): void
    {

        $source = __DIR__ . "/" . $name;
        $data = $this->get_file_info($name);

        if ($data['status']) {

            $key = $this->rsa_encrypt_decrypt->decrypt($data["data"]['access_key']);
            $key = hex2bin($key);
            $local_file = $data["data"]["file_name"];
            $this->ftp->get_file($source, $file);
            // decryptFile($source, $key, __DIR__ . "/temp/" . $local_file);
            $destination = __DIR__ . "/temp/" . $local_file;
            $this->aes_file->decryptFile($source, $key, $destination);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename(__DIR__ . "/temp/" . $local_file));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize(__DIR__ . "/temp/" . $local_file));
            flush();
            readfile(__DIR__ . "/temp/" . $local_file);
            unlink(__DIR__ . "/temp/" . $local_file);
            unlink($source);
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
        die();
    }

    public function upload_file($file, $destination, $filename, $type, $user_upload_id = null): array
    {
        if ($user_upload_id != null) {
            $this->rsa_encrypt_decrypt = new \RSA\EncryptDecryptRSA($user_upload_id, "user_");
        }

        $checksum = $this->create_checksum($file);
        $key = $this->create_key();
        $encrypt_name = $this->create_encrypt_name();

        $save_info = $this->save_file_info($encrypt_name, $key, $destination, $checksum, $filename, $type);

        if ($save_info['status']) {

            $source = __DIR__ . "/temp/" . $encrypt_name;
            $this->aes_file->encrypt_file($file, $key, $source);
            // encrypt_file($file, $key, __DIR__ . "/temp/" . $encrypt_name);
            $fp = fopen($source, 'r');
            $this->ftp->send_file($fp, $destination, $encrypt_name);
            unlink($source);
        }

        return $save_info;
    }

    public function delete_file($id, $pwd = "."): array
    {
        $directory = $pwd . "/" . $id;
        $this->sedjm->clear_all();
        $this->sedjm->set_where("encrypt_file_name", $id, "=");
        $this->sedjm->set_where("directory", $pwd, "=");
        if ($this->ftp->check_type_of_directory($directory) == "file") {
            $this->ftp->remove_file($directory);
        } else {
            $this->ftp->remove_catalogue_recursive($directory);
        }
        $response = $this->sedjm->delete($this->files_table);

        return $response;
    }

    public function create_catalogue($name, $pwd): bool
    {
        $new_catalogue = $pwd . "/" . $name;
        return $this->ftp->create_catalogue($new_catalogue);
    }

    private function get_ftp_connect_data(): array
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

        if (!empty($data)) {
            return $data[0];
        } else {
            return [];
        }
    }

    private function concat_files($file): array
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

    private function save_file_info($encrypt_file_name, $key, $destination, $checksum, $filename, $type): array
    {
        $key = bin2hex($key);
        $key = $this->rsa_encrypt_decrypt->encrypt($key);

        $data = [
            "user_id" => \ModuleManager\Main::$token['payload']->user_id,
            "project_id" => $this->project_id,
            "ftp_id" => $this->connect_data["ftp_id"],
            "file_name" => $filename,
            "encrypt_file_name" => $encrypt_file_name,
            "file_extension" => $type,
            "directory" => $destination,
            "access_key" => $key,
            "create_time" => time(),
            "checksum" => $checksum
        ];
        return $this->sedjm->insert($data, $this->files_table);
    }

    private function get_file_info($encrypt_file_name): array
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
        return md5(rand() . time() . uniqid());
    }

    private function encrypt_key($key)
    {

    }

    private function decrypt_key($encrypt_key)
    {

    }
}
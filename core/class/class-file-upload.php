<?php

namespace ModuleManager\FileUpload;

interface IFileUploader
{
    /**
     * @param int $user_id User witch upload file
     * @param array $file File to upload
     * @param string $type which type have file |||
     */
    public function upload_file(int $user_id, array $file, string $type): array;
}

class FileUploader implements IFileUploader
{
    private array $allowed_extensions = [];
    private string $upload_dir = "";
    private string $main_path = __DIR__ . "/../..";

    public function __construct(array $allowed_extensions = [], $upload_dir = "uploads")
    {
        $this->allowed_extensions = $allowed_extensions;
        $this->upload_dir = $upload_dir;

    }

    private function create_name($file_extension): string
    {
        $name = md5(time() . rand()) . '.' . $file_extension;
        return $name;
    }

    private function save_file_in_database($path, $name, $user_id): array
    {
        global $main;
        $table = "UploadedFile";
        $data = [
            "user_id" => $user_id,
            "path" => $path,
            "name" => $name,
            "date" => time()
        ];

        $data = $main->sedjm->insert($data, $table);
        return $data;
    }

    public function upload_file(int $user_id, array $file, string $type = ""): array
    {
        $error = [];
        $status = false;

        $allowed_extensions_string = implode(', ', $this->allowed_extensions);

        $filename = $file['name'];
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $new_filename = $this->create_name($file_extension);

        $destination = $this->main_path . '/' . $this->upload_dir . '/' . $new_filename;

        // Sprawdzenie rozszerzenia pliku
        if (!empty($this->allowed_extensions) && !in_array($file_extension, $this->allowed_extensions)) {
            $error[] = "Nieprawidłowe rozszerzenie pliku. Dozwolone rozszerzenia: " . $allowed_extensions_string;
        } else {

            // Przeniesienie pliku do folderu docelowego
            $move_file = move_uploaded_file($file['tmp_name'], $destination);
            $save_in_db = $this->save_file_in_database($this->upload_dir . '/' . $new_filename, $filename, $user_id);

            if ($move_file && $save_in_db['status']) {
                $status = true;
            } else {
                $error[] = "Wystąpił błąd podczas przesyłania pliku.";
            }

        }


        return [
            "status" => $status,
            "errors" => $error,
            "file" => $filename,
            "file_path" => $this->upload_dir . '/' . $new_filename
        ];
    }

    public function upload_base64(int $user_id, string $base64, string $name): array
    {
        $error = [];
        $status = false;


        $filename = $name;
        $file_extension = "jpg";
        $new_filename = $this->create_name($file_extension);

        $destination = $this->main_path . '/' . $this->upload_dir . '/' . $new_filename;


        // Przeniesienie pliku do folderu docelowego
        $move_file = file_put_contents($destination, base64_decode($base64));

        $save_in_db = $this->save_file_in_database($this->upload_dir . '/' . $new_filename, $filename, $user_id);

        if ($move_file && $save_in_db['status']) {
            $status = true;
        } else {
            $error[] = "Wystąpił błąd podczas przesyłania pliku.";
        }


        return [
            "status" => $status,
            "errors" => $error,
            "file" => $filename,
            "file_path" => $this->upload_dir . '/' . $new_filename
        ];
    }

}
<?php

class FTP
{
    // TODO: change fto to ftp_ssl_connect
    private \FTP\Connection $ftp;

    public function __construct($server, $port, $user, $password)
    {

        $this->ftp = ftp_connect($server, $port);
        $login_result = @ftp_login($this->ftp, $user, $password);

        if ($login_result) {
            // echo "Connected as $user@$server\n";
        } else {
            // echo "Couldn't connect as $user\n";
        }

        ftp_pasv($this->ftp, true); // Ustawienie trybu pasywnego

    }

    public function create_catalogue($name)
    {
        try {
            ftp_mkdir($this->ftp, $name);
        } catch (\Throwable $th) {
            echo $th->__toString();
        }
    }

    public function send_file($file, $destination_file, $filename)
    {
        try {
            // $file_name = explode("/", $source_file);
            $remote_filename = $destination_file . "/" . $filename;
            $ret = ftp_fput($this->ftp, $remote_filename, $file, FTP_BINARY);

        } catch (\Throwable $th) {
            echo $th->__toString();
        }
    }

    public function change_directory($directory)
    {
        if (!@ftp_chdir($this->ftp, $directory)) {
            die("Could not change directory to {$directory}");
        } else {
            // echo "Changed directory to {$directory}\n";
        }
    }

    public function remove_catalogue_recursive($directory)
    {

        $start_directory = ftp_pwd($this->ftp);
        $lists = $this->list_files($directory);
        foreach ($lists as $list) {
            $full = $list['pwd'] . '/' . $list['name'];

            if ($list['type'] == 'catalogue') {

                if (@$this->remove_catalogue_recursive($full)) {
                    // echo "usunięto folder \n";
                } else {
                    // echo "Nie udało się usunąć folderu \n";
                }
            } else {

                $this->remove_file($directory);

            }
        }

        $this->change_directory($start_directory);
        if (@ftp_rmdir($this->ftp, $directory)) {
            return true;
        } else {
            return false;

        }
    }

    public function remove_file($directory)
    {

        if (@ftp_delete($this->ftp, $directory)) {
            // echo "usunięto plik \n";
        } else {
            // echo "Nie udało się usunąć pliku \n";
        }


    }

    public function list_files($directory = ".")
    {
        $file_list = [];

        $this->change_directory($directory);
        $current_dir = ftp_pwd($this->ftp);

        $lists = ftp_rawlist($this->ftp, $current_dir);

        foreach ($lists as $key => $value) {

            $chunks = preg_split("/\s+/", $value);

            list($rights, $number, $user, $group, $size, $month, $day, $time) = $chunks;

            array_splice($chunks, 0, 8);
            $name = implode(" ", $chunks);
            $file_list[] = [
                'pwd' => $current_dir,
                'type' => $rights[0] === 'd' ? 'directory' : 'file',
                'permission' => $rights,
                'name' => $name,
                'size' => $size,
                'modify_time' => $month . " " . $day . " " . $time,
            ];

        }
        return $file_list;
    }

    public function rename_file($from, $to)
    {
        if (@ftp_rename($this->ftp, $from, $to)) {
            // echo "przeniesiono";
        } else {
            // echo "Wystąpił problem podczas poznoszenia pliku";
        }
    }

    public function get_file($local_file, $remote_file)
    {
        if (@ftp_get($this->ftp, $local_file, $remote_file)) {
            // echo "pobrano";
        } else {
            // echo "Wystąpił problem podczas pobierania pliku";
        }
    }

    public function __destruct()
    {
        ftp_close($this->ftp);
    }
}

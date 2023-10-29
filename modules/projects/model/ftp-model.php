<?php

class FTPModel
{
    private $ftp_id;
    private $project_id;
    private $serwer;
    private $user;
    private $password;
    private $port;

    public function __construct(
        $ftp_id,
        $project_id,
        $serwer,
        $user,
        $password,
        $port
    ) {
        $this->ftp_id = $ftp_id;
        $this->project_id = $project_id;
        $this->serwer = $serwer;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
    }

    // getters

    public function get_ftp_id()
    {
        return $this->ftp_id;
    }
    public function get_project_id()
    {
        return $this->project_id;
    }
    public function get_serwer()
    {
        return $this->serwer;
    }
    public function get_user()
    {
        return $this->user;
    }
    public function get_password()
    {
        return $this->password;
    }
    public function get_port()
    {
        return $this->port;
    }

    // Setters

    public function set_ftp_id($ftp_id)
    {
        $this->ftp_id = $ftp_id;
    }
    public function set_project_id($project_id)
    {
        $this->project_id = $project_id;
    }
    public function set_serwer($serwer)
    {
        $this->serwer = $serwer;
    }
    public function set_user($user)
    {
        $this->user = $user;
    }
    public function set_password($password)
    {
        $this->password = $password;
    }
    public function set_port($port)
    {
        $this->port = $port;
    }
}
?>
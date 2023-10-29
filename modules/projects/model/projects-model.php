<?php

namespace Projects\Model;

class ProjectsModel
{
    private $project_id;
    private $name;
    private $description;
    private $owner_id;
    private $photo;
    private $status;

    public function __construct($project_id, $owner_id, $name, $description, $photo, $status)
    {
        $this->project_id = $project_id;
        $this->owner_id = $owner_id;
        $this->name = $name;
        $this->description = $description;
        $this->photo = $photo;
        $this->status = $status;
    }

    // getters

    public function get_id()
    {
        return $this->project_id;
    }

    public function get_name()
    {
        return $this->name;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function get_owner_id()
    {
        return $this->owner_id;
    }

    public function get_photo()
    {
        return $this->photo;
    }

    public function get_status()
    {
        return $this->status;
    }

    // Setters

    public function set_name($name)
    {
        $this->name = $name;
    }

    public function set_description($description)
    {
        $this->description = $description;
    }

    public function set_owner_id($owner_id)
    {
        $this->owner_id = $owner_id;
    }

    public function set_photo($photo)
    {
        $this->photo = $photo;
    }

    public function set_status($status)
    {
        $this->status = $status;
    }

}
?>
<?php
class CollaboratorsModel
{
    private $collaborator_id;
    private $project_id;
    private $user_id;
    private $role;
    private $status;

    public function __construct(
        $collaborator_id,
        $project_id,
        $user_id,
        $role,
        $status
    ) {
        $this->collaborator_id = $collaborator_id;
        $this->project_id = $project_id;
        $this->user_id = $user_id;
        $this->role = $role;
        $this->status = $status;
    }

    // Getter
    public function get_collaborator_id()
    {
        return $this->collaborator_id;
    }

    public function get_project_id()
    {
        return $this->project_id;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_role()
    {
        return $this->role;
    }

    public function get_status()
    {
        return $this->status;
    }

    // Setters

    public function set_collaborator_id($collaborator_id)
    {
        $this->collaborator_id = $collaborator_id;
    }

    public function set_project_id($project_id)
    {
        $this->project_id = $project_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function set_role($role)
    {
        $this->role = $role;
    }

    public function set_status($status)
    {
        $this->status = $status;
    }

}
?>
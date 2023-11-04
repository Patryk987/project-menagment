<?php

interface ProjectsRepositoryInterface extends RepositoryInterface
{
    public function get_collaborators(int $project_id): ?array;
    public function get_user_projects(int $user_id): ?array;
}


/**
 * Repository for projects
 */
class ProjectsRepository implements ProjectsRepositoryInterface
{
    private $main;

    private $project_table = "Projects";
    private $collaborator_table = "Collaborators";
    private $user_table = "Users";

    public function __construct()
    {
        global $main;
        $this->main = &$main;
    }

    public function get_all()
    {

        $this->main->sedjm->clear_all();

        $data = $this->main->sedjm->get(
            [
                "project_id",
                "owner_id",
                "name",
                "description",
                "photo_url",
                "status",
                "create_time",
                [
                    "column" => "project_id",
                    "alias" => "collaborators",
                    "table" => $this->project_table,
                    "function" => [$this, "get_collaborators"]
                ]
            ],
            $this->project_table
        );

        return $data;

    }

    public function get_by_id($id)
    {

        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_where("project_id", $id, "=");
        $data = $this->main->sedjm->get(
            [
                "project_id",
                "owner_id",
                "name",
                "description",
                "photo_url",
                "status",
                "create_time",
                [
                    "column" => "project_id",
                    "alias" => "collaborators",
                    "table" => $this->project_table,
                    "function" => [$this, "get_collaborators"]
                ]
            ],
            $this->project_table
        );

        return $data;

    }

    public function create(array $data)
    {

        $this->main->sedjm->clear_all();
        $this->main->sedjm->insert($data, $this->project_table);

    }

    public function update($id, array $data)
    {

        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_where("project_id", $id, "=");
        $this->main->sedjm->update($data, $this->project_table);

    }

    public function delete($id)
    {
        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_where("project_id", $id, "=");
        $this->main->sedjm->delete($this->project_table);
    }

    public function get_collaborators(int $project_id): ?array
    {
        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_join(
            "LEFT",
            [
                'table' => $this->user_table,
                'column' => "user_id"
            ],
            [
                'table' => $this->collaborator_table,
                'column' => "user_id"
            ],
        );
        $this->main->sedjm->set_where("project_id", $project_id, "=");

        $data = $this->main->sedjm->get(
            [
                "collaborator_id",
                "project_id",
                "user_id",
                "role",
                "status",
                [
                    "column" => "nick",
                    "alias" => "nick",
                    "table" => $this->user_table
                ],
                [
                    "column" => "email",
                    "alias" => "email",
                    "table" => $this->user_table
                ],
                [
                    "column" => "status",
                    "alias" => "user_status",
                    "table" => $this->user_table
                ]
            ],
            $this->collaborator_table
        );

        return $data;
    }

    public function get_user_projects(int $user_id): ?array
    {
        $output = [];
        $projects_id = array_merge($this->user_as_admin($user_id), $this->user_as_collaborators($user_id));

        foreach ($projects_id as $project_id) {
            $output[] = (int) $project_id["project_id"];
            // $output[] = $this->get_by_id($project_id["project_id"]);
        }

        return $output;

    }

    /**
     * List of projects where the user is an administrator
     */
    private function user_as_admin($user_id): ?array
    {
        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_where("owner_id", $user_id, "=");
        $data = $this->main->sedjm->get(["project_id"], $this->project_table);

        return $data;
    }

    /**
     * List of projects where the user is an collaborators
     */
    private function user_as_collaborators($user_id): ?array
    {
        $this->main->sedjm->clear_all();
        $this->main->sedjm->set_where("user_id", $user_id, "=");
        $data = $this->main->sedjm->get(["project_id"], $this->collaborator_table);

        return $data;
    }
}
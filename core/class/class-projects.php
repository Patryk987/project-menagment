<?php

class Projects
{
    private string $project_table_name = "Projects";
    private int $project_id;
    private int $user_id;
    private ProjectsRepository $repository;
    private $main;
    public function __construct(int $user_id, string $projects_id)
    {

        $this->repository = new ProjectsRepository;
        $this->project_id = (int) $projects_id;
        $this->user_id = $user_id;

    }

    /**
     * Check if user have access to this project and return data about this
     */
    public function get_project_data(): ProjectModel
    {
        $output = new ProjectModel(ProjectStatus::BLOCKED);
        $user_projects = $this->repository->get_user_projects($this->user_id);
        $projects_details = $this->repository->get_by_id($this->project_id);

        if (!empty($projects_details)) {

            if (in_array($projects_details[0]['project_id'], $user_projects)) {

                $output->set_status(ProjectStatus::from($projects_details[0]['status']));
                $output->set_project_id($projects_details[0]['project_id']);
                $output->set_owner_id($projects_details[0]['owner_id']);
                $output->set_name($projects_details[0]['name']);
                $output->set_description($projects_details[0]['description']);
                $output->set_photo_url($projects_details[0]['photo_url']);
                $output->set_create_time($projects_details[0]['create_time']);
                $output->set_collaborators($projects_details[0]['collaborators']);
            }

        }

        return $output;

    }

}
<?php

class ProjectModel
{
    private ProjectStatus $status;
    private ?int $project_id;
    private ?int $owner_id;
    private ?string $name;
    private ?string $description;
    private ?string $photo_url;
    private ?string $create_time;
    private ?array $collaborators;

    public function __construct(ProjectStatus $status, ?int $project_id = null, ?int $owner_id = null, ?string $name = null, ?string $description = null, ?string $photo_url = null, ?string $create_time = null, ?array $collaborators = null)
    {
        $this->project_id = $project_id;
        $this->owner_id = $owner_id;
        $this->name = $name;
        $this->description = $description;
        $this->photo_url = $photo_url;
        $this->status = $status;
        $this->create_time = $create_time;
        $this->collaborators = $collaborators;
    }

    public function get_project_id(): int
    {
        return $this->project_id;
    }

    public function get_owner_id(): int
    {
        return $this->owner_id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_description(): string
    {
        return $this->description;
    }

    public function get_photo_url(): string
    {
        return $this->photo_url;
    }

    public function get_status(): ProjectStatus
    {
        return $this->status;
    }

    public function get_create_time(): string
    {
        return $this->create_time;
    }

    public function get_collaborators(): array
    {
        return $this->collaborators;
    }

    // Setter

    public function set_project_id(int $project_id): void
    {
        $this->project_id = $project_id;
    }

    public function set_owner_id(int $owner_id): void
    {
        $this->owner_id = $owner_id;
    }

    public function set_name(string $name): void
    {
        $this->name = $name;
    }

    public function set_description(string $description): void
    {
        $this->description = $description;
    }

    public function set_photo_url(string $photo_url): void
    {
        $this->photo_url = $photo_url;
    }

    public function set_status(ProjectStatus $status): void
    {
        $this->status = $status;
    }

    public function set_create_time(string $create_time): void
    {
        $this->create_time = $create_time;
    }

    public function set_collaborators(array $collaborators): void
    {
        $this->collaborators = $collaborators;
    }
}

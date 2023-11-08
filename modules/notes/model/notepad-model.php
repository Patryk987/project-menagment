<?php

namespace Notes\Model;

class NotepadModel
{
    private int $notepad_id;
    private int $project_id;
    private int $author_id;
    private string $name;
    private string $icon_url;
    private string $background;
    private string $default_view;
    private string $create_time;
    private string $update_time;

    public function __construct(
        int $notepad_id,
        int $project_id,
        int $author_id,
        string $name,
        string $icon_url,
        string $background,
        string $default_view,
        string $create_time,
        string $update_time
    ) {
        $this->notepad_id = $notepad_id;
        $this->project_id = $project_id;
        $this->author_id = $author_id;
        $this->name = $name;
        $this->icon_url = $icon_url;
        $this->background = $background;
        $this->default_view = $default_view;
        $this->create_time = $create_time;
        $this->update_time = $update_time;
    }

    // SET

    public function set_notepad_id(int $notepad_id): void
    {
        $this->notepad_id = $notepad_id;
    }

    public function set_project_id(int $project_id): void
    {
        $this->project_id = $project_id;
    }

    public function set_author_id(int $author_id): void
    {
        $this->author_id = $author_id;
    }

    public function set_name(string $name): void
    {
        $this->name = $name;
    }

    public function set_icon_url(string $icon_url): void
    {
        $this->icon_url = $icon_url;
    }

    public function set_background(string $background): void
    {
        $this->background = $background;
    }

    public function set_default_view(string $default_view): void
    {
        $this->default_view = $default_view;
    }

    public function set_create_time(string $create_time): void
    {
        $this->create_time = $create_time;
    }

    public function set_update_time(string $update_time): void
    {
        $this->update_time = $update_time;
    }

    // GET

    public function get_notepad_id(): int
    {
        return $this->notepad_id;
    }

    public function get_project_id(): int
    {
        return $this->project_id;
    }

    public function get_author_id(): int
    {
        return $this->author_id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_icon_url(): string
    {
        return $this->icon_url;
    }

    public function get_background(): string
    {
        return $this->background;
    }

    public function get_default_view(): string
    {
        return $this->default_view;
    }

    public function get_create_time(): string
    {
        return $this->create_time;
    }

    public function get_update_time(): string
    {
        return $this->update_time;
    }



}

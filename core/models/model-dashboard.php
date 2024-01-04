<?php

namespace Dashboard;

class DashboardModel
{

    private int $width = 1;
    private int $height = 1;
    private mixed $content;
    private string $location;

    public function __construct(mixed $content, string $location, int $width = 1, int $height = 1)
    {
        $this->width = $width;
        $this->height = $height;
        $this->content = $content;
        $this->location = $location;
    }

    // SET

    public function set_width(int $width): void
    {
        $this->width = $width;
    }
    public function set_height(int $height): void
    {
        $this->height = $height;
    }
    public function set_content(mixed $content): void
    {
        $this->content = $content;
    }
    public function set_location(mixed $location): void
    {
        $this->location = $location;
    }

    // GET

    public function get_width(): int
    {
        return $this->width;
    }
    public function get_height(): int
    {
        return $this->height;
    }
    public function get_content(): mixed
    {
        return $this->content;
    }
    public function get_location(): mixed
    {
        return $this->content;
    }

}

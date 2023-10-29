<?php

namespace Projects\Model;

class InsertModel
{
    private bool $status;
    private ?int $id;
    private ?string $error;

    public function __construct(
        bool $status,
        int $id = null,
        string $error = null
    ) {
        $this->status = $status;
        $this->id = $id;
        $this->error = $error;
    }

    public function get_status(): bool
    {
        return $this->status;
    }

    public function set_status(bool $status): void
    {
        $this->status = $status;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    public function get_error(): ?string
    {
        return $this->error;
    }

    public function set_error(string $error): void
    {
        $this->error = $error;
    }
}
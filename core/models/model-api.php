<?php
namespace Models;

class ApiModel
{
    private \ApiStatus $status;
    private ?array $message = null;
    private ?array $error = null;
    private int $code = 500;

    public function __construct(\ApiStatus $status, ?array $message = null, ?array $error = null)
    {
        $this->status = $status;
        $this->message = $message;
        $this->error = $error;
    }

    // SET

    public function set_status(\ApiStatus $status): void
    {
        $this->status = $status;
    }

    public function set_message(array $message): void
    {
        $this->message = $message;
    }

    public function set_error(array $error): void
    {
        $this->error = $error;
    }


    public function set_code(int $code): void
    {
        $this->code = $code;
    }


    // GET

    public function get_status(): \ApiStatus
    {
        return $this->status;
    }

    public function get_message(): ?array
    {
        return $this->message;
    }

    public function get_error(): ?array
    {
        return $this->error;
    }

    public function get_code(): int
    {
        return $this->code;
    }

}

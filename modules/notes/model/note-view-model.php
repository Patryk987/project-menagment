<?php

namespace Notes\Model;

class NoteViewModel
{
    private int $view_id;
    private int $user_id;
    private int $notepad_id;
    private string $view;


    public function __construct(
        int $view_id,
        int $user_id,
        int $notepad_id,
        string $view
    ) {
        $this->view_id = $view_id;
        $this->user_id = $user_id;
        $this->notepad_id = $notepad_id;
        $this->view = $view;
    }

    // SET
    public function set_view_id(int $view_id): void
    {
        $this->view_id = $view_id;
    }

    public function set_user_id(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function set_notepad_id(int $notepad_id): void
    {
        $this->notepad_id = $notepad_id;
    }

    public function set_view(string $view): void
    {
        $this->view = $view;
    }

    // GET

    public function get_view_id(): int
    {
        return $this->view_id;
    }

    public function get_user_id(): int
    {
        return $this->user_id;
    }

    public function get_notepad_id(): int
    {
        return $this->notepad_id;
    }

    public function get_view(): string
    {
        return $this->view;
    }



}

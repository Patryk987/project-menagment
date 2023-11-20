<?php

namespace Notes\Model;

class NotesModel
{

    private int $note_id;
    private int $notepad_id;
    private int $author_id;
    private string $title;
    private string $note;
    private string $create_time;
    private string $update_time;

    public function __construct(
        int $note_id,
        int $notepad_id,
        int $author_id,
        string $title,
        string $note,
        string $create_time,
        string $update_time
    ) {
        $this->note_id = $note_id;
        $this->notepad_id = $notepad_id;
        $this->author_id = $author_id;
        $this->title = $title;
        $this->note = $note;
        $this->create_time = $create_time;
        $this->update_time = $update_time;
    }

    // SET

    public function set_note_id(int $note_id): void
    {
        $this->note_id = $note_id;
    }

    public function set_notepad_id(int $notepad_id): void
    {
        $this->notepad_id = $notepad_id;
    }

    public function set_author_id(int $author_id): void
    {
        $this->author_id = $author_id;
    }

    public function set_title(string $title): void
    {
        $this->title = $title;
    }

    public function set_note(string $note): void
    {
        $this->note = $note;
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

    public function get_note_id(): int
    {
        return $this->note_id;
    }
    public function get_notepad_id(): int
    {
        return $this->notepad_id;
    }
    public function get_author_id(): int
    {
        return $this->author_id;
    }
    public function get_title(): string
    {
        return $this->title;
    }
    public function get_note(): string
    {
        return $this->note;
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

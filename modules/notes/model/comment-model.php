<?php

namespace Notes\Model;

class NoteCommentModel
{
    private int $comment_id;
    private int $user_id;
    private int $note_id;
    private string $comment;
    private string $create_time;

    public function __construct(
        int $comment_id,
        int $user_id,
        int $note_id,
        string $comment,
        string $create_time
    ) {
        $this->comment_id = $comment_id;
        $this->user_id = $user_id;
        $this->note_id = $note_id;
        $this->comment = $comment;
        $this->create_time = $create_time;
    }

    // SET 

    public function set_comment_id(int $comment_id): void
    {
        $this->comment_id = $comment_id;
    }
    public function set_user_id(int $user_id): void
    {
        $this->user_id = $user_id;
    }
    public function set_note_id(int $note_id): void
    {
        $this->note_id = $note_id;
    }
    public function set_comment(string $comment): void
    {
        $this->comment = $comment;
    }
    public function set_create_time(string $create_time): void
    {
        $this->create_time = $create_time;
    }

    // GET

    public function get_comment_id(): int
    {
        return $this->comment_id;
    }
    public function get_user_id(): int
    {
        return $this->user_id;
    }
    public function get_note_id(): int
    {
        return $this->note_id;
    }
    public function get_comment(): string
    {
        return $this->comment;
    }
    public function get_create_time(): string
    {
        return $this->create_time;
    }
}

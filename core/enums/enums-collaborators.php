<?php

enum CollaboratorType: int
{
    case ADMIN = 1;
    case COLLABORATOR = 2;
    case WATCHER = 3;

    public function get_name(): string
    {
        return match ($this) {
            self::ADMIN => "Admin",
            self::COLLABORATOR => "Collaborator",
            self::WATCHER => "Watcher"
        };
    }
}



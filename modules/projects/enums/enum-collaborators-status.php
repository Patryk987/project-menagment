<?php

enum CollaboratorsRole: int
{
    case CLIENT = 1;
    case COLLABORATORS = 2;
    case ADMIN = 3;

    public function get_name(): string
    {
        return match ($this) {
            self::CLIENT => "Client",
            self::COLLABORATORS => "Collaborators",
            self::ADMIN => "Admin"
        };
    }
}



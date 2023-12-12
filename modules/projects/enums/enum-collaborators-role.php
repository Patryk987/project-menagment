<?php

enum CollaboratorsStatus: int
{
    case ACTIVE = 1;
    case BLOCKED = 2;

    public function get_name(): string
    {
        return match ($this) {
            self::ACTIVE => "Active",
            self::BLOCKED => "Blocked",
        };
    }
}


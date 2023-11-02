<?php

enum ProjectStatus: int
{
    case ACTIVE = 1;
    case ARCHIVE = 2;
    case BLOCKED = 3;

    public function get_name(): string
    {
        return match ($this) {
            self::ACTIVE => "Active",
            self::ARCHIVE => "Archive",
            self::BLOCKED => "Blocked"
        };
    }
}



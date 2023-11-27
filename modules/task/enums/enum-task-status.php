<?php

namespace Tasks\Enums;

enum TaskStatus: int
{
    case ACTIVE = 1;
    case ARCHIVED = 2;

    public function get_name(): string
    {
        return match ($this) {
            self::ACTIVE => "Active",
            self::ARCHIVED => "Archived",
        };
    }

    public function get_bool_status(): bool
    {
        return match ($this) {
            self::ACTIVE => true,
            self::ARCHIVED => false,
        };
    }
}



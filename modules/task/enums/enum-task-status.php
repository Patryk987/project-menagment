<?php

namespace Tasks\Enums;

enum TaskStatus: int
{
    case ACTIVE = 1;
    case CHECKED = 2;
    case ARCHIVED = 3;

    public function get_name(): string
    {
        return match ($this) {
            self::ACTIVE => "Active",
            self::CHECKED => "Checked",
            self::ARCHIVED => "Archived",
        };
    }

    public function get_bool_status(): bool
    {
        return match ($this) {
            self::ACTIVE => false,
            self::CHECKED => true,
            self::ARCHIVED => true,
        };
    }
}



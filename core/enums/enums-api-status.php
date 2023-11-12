<?php

enum ApiStatus: int
{
    case CORRECT = 1;
    case ERROR = 2;
    case WARRING = 3;

    public function get_name(): string
    {
        return match ($this) {
            self::CORRECT => "Correct",
            self::ERROR => "Error",
            self::WARRING => "Warring"
        };
    }

    public function get_bool_status(): bool
    {
        return match ($this) {
            self::CORRECT => true,
            self::ERROR => false,
            self::WARRING => true
        };
    }
}



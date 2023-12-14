<?php

namespace Issues\Enums;

enum IssuesStatus: int
{
    case TODO = 1;
    case INPROGRESS = 2;
    case TEST = 3;
    case DONE = 4;
    case ARCHIVE = 5;

    public function get_name(): string
    {
        return match ($this) {
            self::TODO => "Todo",
            self::INPROGRESS => "In progress",
            self::TEST => "Test",
            self::DONE => "Done",
            self::ARCHIVE => "Archive",
        };
    }
}


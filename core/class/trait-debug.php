<?php

namespace ModuleManager;

trait Debug
{
    public static array $error_list = [];
    public static array $warning_list = [];
    public static array $results = [];
    private float $debug_time;
    static private bool $debug_status = false;
    private function start_debug(bool $debug_status = false): void
    {
        static::$debug_status = $debug_status;

        if (static::$debug_status == true) {
            $this->debug_time = microtime(true);
        } else {
            error_reporting(0);

            $sec = new \SimpleWAF();
            $sec->secureMe(true);

        }
    }
    public static function set_error(string $name, string $type, array $details = []): void
    {
        if (static::$debug_status) {

        }

        static::$error_list[] = [
            "name" => $name,
            "type" => $type,
            "details" => $details
        ];
    }
    public static function set_warning(string $name, int $type = 0, string $details = ""): void
    {

        static::$warning_list[] = [
            "name" => $name,
            "type" => $type,
            "details" => $details
        ];

    }
    public static function get_debug_status(): bool
    {
        return static::$debug_status;
    }

    // Trigger uncatch error

    // Obsługuje wyjątki
    public function exceptionHandler($exception)
    {
        echo "Warrning: " . $exception->getMessage();
        // Możesz tutaj wykonać dodatkowe czynności, takie jak logowanie

        $details = [
            "message" => $exception->getMessage()
        ];
        \ModuleManager\Main::set_error('Unexpected warning', 'WARNING', $details);
    }

    // Obsługuje błędy
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        echo "Error: [$errno] $errstr in $errfile, line: $errline";

        $details = [
            "message" => $errstr,
            "file" => $errfile,
            "line" => $errline
        ];
        \ModuleManager\Main::set_error('Unexpected error', 'ERROR', $details);
    }


    private function formatPeriod($endtime, $starttime)
    {

        $duration = $endtime - $starttime;

        $hours = (int) ($duration / 60 / 60);

        $minutes = (int) ($duration / 60) - $hours * 60;

        $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;

        $microseconds = intval(($duration - $hours * 60 * 60 - $minutes * 60) * 1000000);

        $hours = ($hours == 0 ? "00" : $hours);
        $minutes = ($minutes == 0 ? "00" : ($minutes < 10 ? "0" . $minutes : $minutes));
        $seconds = ($seconds == 0 ? "00" : ($seconds < 10 ? "0" . $seconds : $seconds));

        return $hours . ":" . $minutes . ":" . $seconds . ":" . $microseconds;
    }

    private function end_debug(): array
    {
        if (static::$debug_status == true) {

            $debug_time_end = microtime(true);

            $results = [
                "working_time" => $this->formatPeriod($debug_time_end, $this->debug_time),
                "error_list" => static::$error_list
            ];
            static::$results = $results;
            return $results;

        } else {
            return [];
        }


    }
}
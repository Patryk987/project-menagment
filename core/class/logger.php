<?php

namespace ModuleManager;

class Logger
{
    private $logFile;

    public function __construct($logFileName)
    {
        $this->logFile = fopen($logFileName, 'a');
    }

    public function log($message, $logLevel = 'INFO')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$logLevel] $message\n";
        fwrite($this->logFile, $logEntry);
    }

    public function close()
    {
        fclose($this->logFile);
    }
}
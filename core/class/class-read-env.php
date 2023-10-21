<?php

namespace ModuleManager;

/**
 * Function to read ENV file
 */
class LoadEnv
{
    private string $envFilePath;

    public function __construct(string $path)
    {
        $this->envFilePath = $path . '/.env';
    }

    private function setEnv(string $name, string $value): void
    {
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }

    public function initEnv(): void
    {

        if (file_exists($this->envFilePath)) {

            $envVariables = parse_ini_file($this->envFilePath, false, INI_SCANNER_RAW);

            foreach ($envVariables as $name => $value) {

                $name = trim($name);
                $value = trim($value);

                $this->setEnv($name, $value);

            }
        }

    }

}
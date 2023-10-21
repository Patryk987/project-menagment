<?php


namespace ModuleManager;

trait LoadFile
{
    private function find_class_in_string($string)
    {

        $pos = strpos($string, ":");
        if ($pos !== false) {
            return explode(":", $string)[0];
        } else {
            return null;
        }

    }

    private function find_function_in_string($string)
    {

        $pos_1 = strpos($string, ":");
        $pos_2 = strpos($string, " ");

        if ($pos_1 !== false) {
            $string = explode(":", $string)[1];
        }

        if ($pos_2 !== false) {
            $string = explode(" ", $string)[0];
        }

        return $string;

    }

    private function find_parameter_in_string($string)
    {

        $pos = strpos($string, " ");

        if ($pos !== false) {

            $parameters = explode(" ", $string);
            $parameters = explode(",", $parameters[1]);
            $r_parameters = [];
            foreach ($parameters as $key => $parameter) {
                // if ($key != 0) {

                $r_parameters[] = $parameter;
                // }
            }

            return $r_parameters;

        } else {
            return [];
        }

    }

    private function load_function($function_string)
    {
        $string = $function_string[2];
        $class = $this->find_class_in_string($string);
        $function = $this->find_function_in_string($string);
        $parameter = $this->find_parameter_in_string($string);

        if ($class != null) {
            $par_1 = [$class, $function];
        } else {
            $par_1 = $function;
        }

        try {

            $binder = DataBinder::get_binder($function);
            return @call_user_func($binder, $parameter);

        } catch (\Throwable $th) {

            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            Main::set_error('Incorrect function', 'ERROR', $details);

        }
    }

    private function get_page($link): string
    {
        $page = file_get_contents($link, true);
        $pattern = '/(\{{)(.*)(\}})/i';

        return preg_replace_callback($pattern, [$this, 'load_function'], $page);
    }

}
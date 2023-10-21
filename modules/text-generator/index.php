<?php

// traits page



class TextGenerator
{

    use \ModuleManager\LoadFile;
    public function init_module()
    {

        ModuleManager\DataBinder::set_binder(
            [
                "key" => "absolutePatch",
                "function" => [$this, "absolutePatch"]
            ]
        );

        ModuleManager\DataBinder::set_binder(
            [
                "key" => "get_text_generator",
                "function" => [$this, "text_generator"]
            ]
        );

    }

    public function text_generator()
    {

        return $this->get_page(__DIR__ . "/data/index.html");
    }

    function absolutePatch()
    {
        return __DIR__;
    }


}

$user_data = new TextGenerator;
$user_data->init_module();
<?php

class TextGenerator
{

    use \ModuleManager\LoadFile;
    public function init_module()
    {
        // Add style
        \InjectStyles::set_style(["name" => "text-editor-style", "style" => "/modules/text-editor/assets/style/style.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "text-editor-js", "src" => "/modules/text-editor/assets/js/script.js"]);

        ModuleManager\DataBinder::set_binder(
            [
                "key" => "text_editor",
                "function" => [$this, "text_generator"]
            ]
        );

    }

    public function text_generator()
    {


        return $this->get_page(__DIR__ . "/view/index.html");
    }


}

$user_data = new TextGenerator;
$user_data->init_module();
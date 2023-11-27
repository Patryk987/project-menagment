<?php

class TextGenerator
{

    use \ModuleManager\LoadFile;
    public function init_module()
    {
        // Add style
        \InjectStyles::set_style(["name" => "text-editor-style", "style" => "/modules/text-editor/assets/style/style.css"]);

        // Add js script
        \InjectJavaScript::set_script(["name" => "text-editor-js", "src" => "/modules/text-editor/assets/js/text-editor.js"]);

        // \InjectJavaScript::set_script(["name" => "text-editor-js", "src" => "https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"]);

        // \InjectJavaScript::set_script([
        //     "name" => "init-text-editor",
        //     "type" => "script",
        //     "script" => '

        //     '
        // ]);

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
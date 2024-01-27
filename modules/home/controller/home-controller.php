<?php

namespace Home;

class HomeController
{

    use \ModuleManager\LoadFile;
    private $data;

    function __construct()
    {

        $main_page = [
            "name" => \ModuleManager\Main::$translate->get_text("Home"),
            "link" => "home",
            "function" => [$this, "home"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => false
        ];
        \ModuleManager\Pages::set_modules($main_page);

        // Bind data
        \ModuleManager\DataBinder::set_binder(
            [
                "key" => "home_page_project_list",
                "function" => [$this, "project_list"]
            ]
        );

    }
    public function home()
    {

        return $this->get_page(__DIR__ . "/../view/main-page.html");
    }

    public function project_list()
    {
        return "";
    }

}
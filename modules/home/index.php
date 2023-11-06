<?php

namespace Home;

// TODO: ADD PARENT IMPLEMENTATION
// TODO: ADD Cotnroller

class Home
{


    private $data;

    function __construct()
    {

        $main_page = [
            "name" => "Home",
            "link" => "home",
            "function" => [$this, "home"],
            "permission" => [11],
            "status" => true,
            "icon" => basename(__DIR__) . "/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => false
        ];
        \ModuleManager\Pages::set_modules($main_page);

        $main_page = [
            "name" => "Home",
            "link" => "homeasdf",
            "function" => [$this, "home"],
            "permission" => [11],
            "status" => true,
            "icon" => basename(__DIR__) . "/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => true
        ];
        \ModuleManager\Pages::set_modules($main_page);

    }

    public function home()
    {
        return "";
    }

}

new Home;
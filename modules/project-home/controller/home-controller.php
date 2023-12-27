<?php

namespace Home\Controller;

class HomeProjectController
{
    use \ModuleManager\LoadFile;
    private $data;

    public function init_page()
    {
        $main_page = [
            "name" => "Home",
            "link" => "project-home",
            "function" => [$this, "home"],
            "permission" => [1, 11],
            "status" => true,
            "icon" => basename(__DIR__) . "/../project-home/assets/img/icon.svg",
            "position" => 1,
            "belongs_to_project" => true
        ];
        \ModuleManager\Pages::set_modules($main_page);
    }

    public function home()
    {
        // Dashboard blocks
        \Dashboard\Dashboard::set_new_block([$this, "test"], "project-home", 3, 2);

        return $this->get_page(__DIR__ . "/../view/main-page.html");
    }


    public function test()
    {
        return "Example";
    }

}

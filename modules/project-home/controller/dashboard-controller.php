<?php

namespace HomeProject\Controller;

class ProjectDashboardController
{

    use \ModuleManager\LoadFile;

    public function set_dashboard_blocks()
    {

        // Dashboard blocks
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 1, 1);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 3, 1);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 2, 2);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 2, 2);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 1, 1);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 1, 1);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 1, 1);
        \Dashboard\Dashboard::set_new_block([$this, "empty_block"], "project-home", 1, 1);

    }


    public function welcome_message()
    {
        \InjectStyles::set_style(["name" => "welcome_message_style", "style" => "/modules/home/assets/css/welcome_message.css"]);

        return $this->get_page(__DIR__ . "/../view/welcome-message.html");
    }

    public function empty_block()
    {
        return "";
    }

}
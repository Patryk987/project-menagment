<?php

namespace Home;

class DashboardController
{

    use \ModuleManager\LoadFile;

    public function set_dashboard_blocks()
    {

        // Dashboard blocks
        \Dashboard\Dashboard::set_new_block([$this, "welcome_message"], "home", 2, 2);
        \Dashboard\Dashboard::set_new_block([$this, "arability_projects"], "home", 2, 2);


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

    public function arability_projects()
    {
        \InjectStyles::set_style(["name" => "projects_list_style", "style" => "/modules/home/assets/css/projects-list.css"]);

        $output = "<div class='projects_list'>";
        $output .= "<h2>Twoje projekty</h2>";
        $output .= $this->get_projects_table();
        $output .= "</div>";

        return $output;
    }

    private function get_projects_table()
    {


        $header = [
            \ModuleManager\Main::$translate->get_text("Photo") => ["photo_url"],
            \ModuleManager\Main::$translate->get_text("Name") => ["name"],
            \ModuleManager\Main::$translate->get_text("Description") => ["description"],
        ];

        $table = new \ModuleManager\Table(50);
        $table->set_converter("photo_url", [$this, "display_image"]);
        $table->set_converter("upload_time", ["Helper", "time_to_data"]);

        $table->set_id("project_id");

        return $table->generate_table($this->get_projects_list(), $header);

    }

    private function get_projects_list(): array
    {
        global $main;
        $output = [];
        $project = new \ProjectsRepository;
        $projects_id = $project->get_user_projects(\ModuleManager\Main::$token['payload']->user_id);
        foreach ($projects_id as $project_id) {
            $project_data = $project->get_by_id($project_id);
            if (!empty($project) && $project_data[0]['status'] == \ProjectStatus::ACTIVE->value) {

                $img_src = '/' . $project_data[0]['photo_url'];


                $output[] = [
                    "project_id" => $project_data[0]["project_id"],
                    "owner_id" => $project_data[0]["owner_id"],
                    "name" => $project_data[0]["name"],
                    "description" => $project_data[0]["description"],
                    "photo_url" => $img_src,
                    "status" => $project_data[0]["status"],
                    "create_time" => $project_data[0]["create_time"],
                    "collaborators" => $project_data[0]["collaborators"]
                ];

            }
        }

        return $output;
    }

    public function display_image($url)
    {
        return "<img src='" . $url . "' alt='Project img' style='width: 50px; height: 50px; border-radius: 10px'>";
    }
}
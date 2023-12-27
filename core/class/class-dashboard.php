<?php

namespace Dashboard;

class Dashboard
{
    static array $dash_list;

    public function __construct()
    {

        \ModuleManager\DataBinder::set_binder(
            [
                "key" => "dashboard",
                "function" => [$this, "create_dashboard"]
            ]
        );

    }


    public static function set_new_block(mixed $content, string $location, int $width = 1, int $height = 1)
    {
        static::$dash_list[$location][] = new DashboardModel($content, $location, $width, $height);
    }

    public function create_dashboard(array $location)
    {
        // Add style
        \InjectStyles::set_style(["name" => "add_dashboard_style", "style" => "/panel-template/css/dashboard.css"]);

        $location = $location[0];
        $html = "<div id='dashboard'>";

        foreach (static::$dash_list[$location] as $value) {

            $width = $value->get_width();
            $height = $value->get_height();

            $html .= sprintf("<div class='block width-%s height-%s'>", $width, $height);
            $html .= call_user_func($value->get_content());
            $html .= sprintf("</div>");

        }

        $html .= "</div>";
        return $html;
    }

}
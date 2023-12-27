<?php

namespace ModuleManager;


trait MenagePage
{

    private static array $page_list;

    /**
     * Register new page
     */
    public static function set_pages($data): void
    {
        // panel, api, strona
        static::$page_list[$data['name']] = [
            "file" => $data['file'],
            "permissions_required" => $data['permissions_required'],
        ];

    }

    private function page($last_sub_page): string
    {

        $them_name = $this->config["active_template"];

        if (!empty(static::$page_list[$last_sub_page])) {
            $link = static::$page_list[$last_sub_page];
            $full_link = __DIR__ . "/../../template/" . $them_name . "/" . $link['file'];
            return $this->get_page($full_link);
        } else if (empty($last_sub_page)) {

            $full_link = __DIR__ . "/../../template/" . $them_name . "/" . "index.html";
            return $this->get_page($full_link);

        } else {
            header("HTTP/1.0 404 Not Found");
            $full_link = __DIR__ . "/../../template/" . $them_name . "/" . "404.html";
            return $this->get_page($full_link);
        }

    }

}

trait MenagePanel
{

    private function load_login()
    {
        if (!static::$token["status"]) {
            $full_link = __DIR__ . "/../../panel-template/html/login.html";
            return $this->get_page($full_link);
        } else {
            $this->redirect();
        }
    }

    private function load_delete()
    {
        if (!static::$token["status"]) {
            $full_link = __DIR__ . "/../../panel-template/html/delete-account.html";
            return $this->get_page($full_link);
        } else {
            $this->redirect();
        }
    }

    private function load_registration()
    {
        if (!static::$token["status"]) {
            $full_link = __DIR__ . "/../../panel-template/html/registration.html";
            return $this->get_page($full_link);
        } else {
            $this->redirect();
        }

    }

    private function load_logout()
    {

        global $main;

        $token_id = static::$token['payload']->token_id;
        $main->accounts->logout($token_id);

        $this->redirect("/" . $this->config["pages"]->login);

    }

    private function load_empty_panel_page()
    {

        $full_link = __DIR__ . "/../../panel-template/html/404.html";
        return $this->get_page($full_link);

    }

    private function load_panel_page()
    {

        $full_link = __DIR__ . "/../../panel-template/html/panel.html";
        return $this->get_page($full_link);

    }

    private function load_lower_module($project): string
    {
        $user_permission = static::$token['payload']->permission;

        $lower = "";
        foreach (static::$modules as $key => $value) {

            if (
                in_array($user_permission, $value['access_permission'])
                && (isset($value["belongs_to_project"]) && $value["belongs_to_project"] == $project)
            ) {
                if ($value['type'] == "parent" && (!isset($lower_position) || $value['position'] < $lower_position)) {
                    $lower = $key;
                    $lower_position = $value['position'];
                }

            }

        }

        if (!empty($lower)) {
            return $this->load_module($lower, $project);
        } else {
            return $this->load_empty_panel_page();
        }
    }

    private function load_module($last_page, $project = false)
    {
        global $main;

        try {

            $actual_module = $this->get_modules_data($last_page);
            $module_permission = $actual_module['access_permission'];
            $user_permission = static::$token['payload']->permission;

            if (isset($actual_module["belongs_to_project"]) && $actual_module["belongs_to_project"] == $project) {

                if (static::$token["status"]) {

                    if (
                        empty($module_permission)
                        && $last_page != $this->config["pages"]->panel
                    ) {
                        return $this->load_empty_panel_page();
                    }

                    if (
                        (empty($module_permission) && $last_page == $this->config["pages"]->panel)
                        || in_array($user_permission, $module_permission)
                        || in_array(0, $module_permission)
                    ) {
                        static::$module = $this->get_module($last_page);

                        if ($project) {
                            return $this->load_project_page();
                        } else {
                            return $this->load_panel_page();
                        }
                    }

                    $full_link = __DIR__ . "/../../panel-template/html/no-permission.html";
                    return $this->get_page($full_link);

                } else if (in_array(0, $module_permission)) {

                    static::$module = $this->get_module($last_page);

                    if ($project) {
                        return $this->load_project_page();
                    } else {
                        return $this->load_panel_page();
                    }

                } else {

                    $this->redirect("/" . $this->config["pages"]->login);

                }

            } else {
                return $this->load_lower_module($project);
            }

        } catch (\Throwable $th) {
            $details = [
                "message" => $th->getMessage(),
                "code" => $th->getCode(),
                "file" => $th->getFile(),
                "line" => $th->getLine()
            ];
            \ModuleManager\Main::set_error('Load module', 'ERROR', $details);
        }

    }

    private function get_panel($sub_page_list): string
    {
        $last_page = end($sub_page_list);
        switch ($last_page) {

            case $this->config["pages"]->login:
                return $this->load_login();

            case $this->config["pages"]->delete:
                return $this->load_delete();

            case $this->config["pages"]->registration:
                return $this->load_registration();

            case $this->config["pages"]->logout:
                $this->load_logout();
                break;

            default:
                if (!empty($sub_page_list[1])) {

                    return $this->load_module($last_page, false);

                } else {
                    // TODO: load default project page
                    // return $this->load_empty_panel_page();
                    return $this->load_lower_module(false);


                }

        }

        return "";

    }

}

trait MenageProject
{

    private function load_project_page()
    {

        $full_link = __DIR__ . "/../../panel-template/html/project.html";
        return $this->get_page($full_link);

    }

    private function get_project($sub_page_list)
    {
        $last_page = end($sub_page_list);
        if (!empty($sub_page_list[1])) {


            if (
                self::$project->get_status() == \ProjectStatus::ACTIVE
                || self::$project->get_status() == \ProjectStatus::ARCHIVE
            ) {
                return $this->load_module($last_page, true);
            } else {
                return $this->load_empty_panel_page();
            }

        } else {
            // TODO: load default project page
            return $this->load_empty_panel_page();


        }

    }
}


class Pages
{
    use LoadFile, MenagePage, MenagePanel, API, Modules, MenageProject;

    private array $config;
    private string $base_link;
    private string $actual_link;
    public static array $token;
    public static \ProjectModel $project;
    public static $module;
    public static $project_id;
    public static $sub_page_list;
    public array $sub_pages;

    public function __construct($token, &$config, $sub_pages)
    {

        $this->sub_pages = $sub_pages;

        static::$token = $token;

        $this->config = $config;

        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $this->base_link = $link . "/";
        $this->actual_link = $link . $_SERVER["REQUEST_URI"];


    }

    private function redirect(string $url = ""): void
    {
        header("Location: " . $this->base_link . $this->config["pages"]->panel . $url);
    }


    public function get_last_page()
    {

        $sub_page_list = $this->sub_pages;
        return end($sub_page_list);

    }

    public function load_page()
    {

        $last_sub_page = end($this->sub_pages);



        if (
            !empty(static::$token['payload']->user_id)
            && !empty($this->sub_pages[0])
            && $this->config["pages"]->project == $this->sub_pages[0]
            && !empty($this->sub_pages[1])
        ) {

            $project = new \Projects(static::$token['payload']->user_id, $this->sub_pages[1]);
            self::$project = $project->get_project_data();

        } else {
            if (empty(static::$token['payload']->user_id)) {
                // $this->redirect("/" . $this->config["pages"]->login);
            } else {
                // self::$project = new \ProjectModel(\ProjectStatus::BLOCKED);
            }
        }

        $this->map_modules();

        if (!empty($this->sub_pages[0])) {

            $type = $this->sub_pages[0];

            if (!empty($this->sub_pages[1]))
                $project = $this->sub_pages[1];

            if (!empty($this->sub_pages[2]))
                $module = $this->sub_pages[2];

            if (!empty($this->sub_pages[3]))
                $sub_module = $this->sub_pages[3];

        } else {

            $type = 'page';

            if (!empty($this->sub_pages[1]))
                $page = $this->sub_pages[1];

            if (!empty($this->sub_pages[2]))
                $sub_page = $this->sub_pages[2];

        }


        switch ($type) {
            case 'page':
                echo $this->page($last_sub_page);
                break;
            case $this->config["pages"]->panel:
                echo $this->get_panel($this->sub_pages);
                break;
            case $this->config["pages"]->project:
                echo $this->get_project($this->sub_pages);
                break;
            case $this->config["pages"]->api:
                echo $this->api($last_sub_page);
                break;
            default:
                echo $this->page($last_sub_page);
                break;
        }

        exit();

    }

}
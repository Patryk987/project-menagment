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

    private function load_module($last_page)
    {
        global $main;
        if (static::$token["status"]) {

            $actual_module = $this->get_modules_data($last_page);

            $user_permission = static::$token['payload']->permission;
            $module_permission = $actual_module['access_permission'];



            if (empty($module_permission) && $last_page != $this->config["pages"]->panel) {
                return $this->load_empty_panel_page();
            }

            if (
                (empty($module_permission) && $last_page == $this->config["pages"]->panel)
                || in_array($user_permission, $module_permission)
                || in_array(0, $module_permission)
            ) {
                static::$module = $this->get_module($last_page);
                return $this->load_panel_page();
            }


            $full_link = __DIR__ . "/../../panel-template/html/no-permission.html";
            return $this->get_page($full_link);

        } else {

            $actual_module = $this->get_modules_data($last_page);
            $module_permission = $actual_module['access_permission'];

            if (in_array(0, $module_permission)) {
                static::$module = $this->get_module($last_page);
                return $this->load_panel_page();
            } else {
                $this->redirect("/" . $this->config["pages"]->login);
            }

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

                    $project = new \Projects(static::$token['payload']->user_id, $sub_page_list[1]);
                    self::$project = $project->get_project_data();
                    if (
                        self::$project->get_status() == \ProjectStatus::ACTIVE
                        || self::$project->get_status() == \ProjectStatus::ARCHIVE
                    ) {
                        return $this->load_module($last_page);
                    } else {
                        return $this->load_empty_panel_page();
                    }
                } else {
                    // TODO: load default project page
                    return $this->load_empty_panel_page();
                }

        }

        return "";

    }

}


class Pages
{
    use JWT, LoadFile, MenagePage, MenagePanel, API, Modules;

    private array $config;
    private string $base_link;
    private string $actual_link;
    public static array $token;
    public static \ProjectModel $project;
    public static $module;

    public function __construct($token, &$config)
    {

        static::$token = $token;

        $this->config = $config;

        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";

        $this->base_link = $link . "/";
        $this->actual_link = $link . $_SERVER["REQUEST_URI"];

        $this->map_modules();

    }

    private function redirect(string $url = ""): void
    {
        header("Location: " . $this->base_link . $this->config["pages"]->panel . $url);
    }

    private function get_sub_page(): array
    {

        $sub_pages = str_replace($this->base_link, "", $this->actual_link);
        $sub_pages = explode("?", $sub_pages)[0];
        $sub_pages = explode("/", $sub_pages);

        if (empty(end($sub_pages))) {
            array_pop($sub_pages);
        }

        return $sub_pages;

    }


    public function get_last_page()
    {

        $sub_page_list = $this->get_sub_page();
        return end($sub_page_list);

    }

    public function load_page()
    {

        $sub_page_list = $this->get_sub_page();
        $last_sub_page = end($sub_page_list);

        if (!empty($sub_page_list[0])) {

            $type = $sub_page_list[0];

        } else {

            $type = 'page';

        }


        switch ($type) {
            case 'page':
                echo $this->page($last_sub_page);
                break;
            case $this->config["pages"]->panel:
                echo $this->get_panel($sub_page_list);
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
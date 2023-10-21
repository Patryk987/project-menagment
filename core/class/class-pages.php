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

    private function page($last_sub_page): void
    {

        $them_name = $this->config["active_template"];

        if (!empty(static::$page_list[$last_sub_page])) {
            $link = static::$page_list[$last_sub_page];
            $full_link = "./template/" . $them_name . "/" . $link['file'];
            echo $this->get_page($full_link);
        } else if (empty($last_sub_page)) {

            $full_link = "./template/" . $them_name . "/" . "index.html";
            echo $this->get_page($full_link);

        } else {
            header("HTTP/1.0 404 Not Found");
            $full_link = "./template/" . $them_name . "/" . "404.html";
            echo $this->get_page($full_link);
        }

    }

}

trait MenagePanel
{

    private function load_login()
    {
        if (!static::$token["status"]) {
            $full_link = "./panel-template/html/login.html";
            echo $this->get_page($full_link);
        } else {
            header("Location: " . $this->base_link . $this->config["pages"]->panel);
        }
    }

    private function load_delete()
    {
        if (!static::$token["status"]) {
            $full_link = "./panel-template/html/delete-account.html";
            echo $this->get_page($full_link);
        } else {
            header("Location: " . $this->base_link . $this->config["pages"]->panel);
        }
    }

    private function load_registration()
    {
        if (!static::$token["status"]) {
            $full_link = "./panel-template/html/registration.html";
            echo $this->get_page($full_link);
        } else {
            header("Location: " . $this->base_link . $this->config["pages"]->panel);
        }

    }

    private function load_logout()
    {
        global $main;
        $token_id = static::$token['payload']->token_id;
        $logout_status = $main->accounts->logout($token_id);
        header("Location: " . $this->base_link . $this->config["pages"]->panel . "/" . $this->config["pages"]->login);

    }

    private function load_empty_panel_page()
    {

        $full_link = "./panel-template/html/404.html";
        echo $this->get_page($full_link);

    }
    private function load_panel_page()
    {

        $full_link = "./panel-template/html/panel.html";
        echo $this->get_page($full_link);

    }

    private function panel($sub_page_list): void
    {
        $last_page = end($sub_page_list);
        switch ($last_page) {

            case $this->config["pages"]->login:
                $this->load_login();
                break;

            case $this->config["pages"]->delete:
                $this->load_delete();
                break;

            case $this->config["pages"]->registration:
                $this->load_registration();
                break;

            case $this->config["pages"]->logout:
                $this->load_logout();
                break;

            default:

                if (static::$token["status"]) {

                    $actual_module = $this->get_modules_data($last_page);

                    $user_permission = static::$token['payload']->permission;
                    $module_permission = $actual_module['access_permission'];

                    if (empty($module_permission) && $last_page != $this->config["pages"]->panel) {

                        $this->load_empty_panel_page();
                        exit();

                    }

                    if (
                        (empty($module_permission) && $last_page == $this->config["pages"]->panel)
                        || in_array($user_permission, $module_permission) || in_array(0, $module_permission)
                    ) {

                        $this->load_panel_page();
                        exit();

                    }

                    $full_link = "./panel-template/html/no-permission.html";
                    echo $this->get_page($full_link);

                } else {

                    $actual_module = $this->get_modules_data($last_page);
                    $module_permission = $actual_module['access_permission'];

                    if (in_array(0, $module_permission)) {
                        $this->load_panel_page();
                    } else {

                        header("Location: " . $this->base_link . $this->config["pages"]->panel . "/" . $this->config["pages"]->login);
                    }



                    // header("Location: " . $this->base_link . $this->config["pages"]->panel . "/" . $this->config["pages"]->login);

                }

                break;
        }

    }

}

class Pages
{
    use JWT, LoadFile, MenagePage, MenagePanel, API, Modules;

    private array $config;
    private string $base_link;
    public static array $token;

    public function __construct()
    {

        $this->config = Config::get_config();
        $this->base_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";

        $this->map_modules();
        $this->get_token();

    }

    private function get_token()
    {

        // $token = LocalStorage::get_data("token", 'cookie', true);
        $token = LocalStorage::get_data("token", 'session', true);
        if (empty($token)) {
            $token = LocalStorage::get_data("token", 'cookie', true);
            LocalStorage::set_data("token", $token, 'session', true);
        }

        static::$token = $this->check_token($token);

    }

    private function get_link(): string
    {

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        return $actual_link;

    }

    private function get_sub_page(): array
    {

        $actual_link = $this->get_link();
        $sub_pages = str_replace($this->base_link, "", $actual_link);
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
                $this->page($last_sub_page);
                break;
            case $this->config["pages"]->panel:
                $this->panel($sub_page_list);
                break;
            case $this->config["pages"]->api:
                $this->api($last_sub_page);
                break;
            default:
                $this->page($last_sub_page);
                break;
        }

    }

}
<?php

namespace ModuleManager;

trait Modules
{

    private static array $modules = [];

    /**
     * Register new modules
     * @param string $name 
     * @param string $function
     * @param int $access_permission 
     */
    public static function set_modules(array $module)
    {

        if ($module['status']) {

            static::$modules[$module["link"]] = [
                "link" => $module["link"],
                "name" => $module["name"],
                "function" => $module["function"],
                "access_permission" => $module["permission"],
                "icon" => $module["icon"],
                "position" => !empty($module["position"]) ? $module["position"] : 99,
                "type" => "parent"
            ];

        }

    }

    /**
     * Register new modules
     * @param string $name 
     * @param string $link 
     * @param string $function
     */
    public static function set_child_modules(array $module)
    {
        $parent_link = $module["parent_link"];

        if (!empty(static::$modules[$parent_link])) {

            static::$modules[$module["link"]] = [
                "link" => $module["link"],
                "name" => $module["name"],
                "function" => $module["function"],
                "access_permission" => static::$modules[$parent_link]["access_permission"],
                "icon" => "",
                "type" => "child",
                "parent" => $parent_link,
                "show" => isset($module["show"]) ? $module["show"] : true,
                "position" => 999
            ];
            // static::$modules[$parent_link]['child'][] = [
            //     "link" => $module["link"],
            //     "name" => $module["name"],
            //     "function" => $module["function"],
            //     "access_permission" => static::$modules[$parent_link]["access_permission"],
            //     "icon" => ""
            // ];

        }

    }

    public function map_modules()
    {
        $dir_path = './modules/';
        $modules = scandir($dir_path);


        foreach ($modules as $key => $value) {

            if ($value != ".." && $value != ".") {
                $file_in_module = scandir($dir_path . $value);
                if (in_array("index.php", $file_in_module)) {
                    try {
                        include_once $dir_path . $value . '/index.php';
                    } catch (\Throwable $th) {
                        $details = [
                            "message" => $th->getMessage(),
                            "code" => $th->getCode(),
                            "file" => $th->getFile(),
                            "line" => $th->getLine()
                        ];
                        \ModuleManager\Main::set_error('Include module', 'ERROR', $details);
                    }

                }
            }

        }
    }

    public function get_modules_list()
    {
        $list = [];

        if (!empty(static::$token['payload']->permission)) {

            $user_permission = static::$token['payload']->permission;

        } else {

            $user_permission = 0;

        }

        // sort_array(static::$modules, $key, $type = "ASC")

        foreach (static::$modules as $key => $value) {

            if (in_array($user_permission, $value['access_permission'])) {

                if ($value['type'] == "parent") {

                    $data = [
                        "name" => $value['name'],
                        "link" => $value['link'],
                        "icon" => $value['icon'],
                        "position" => $value['position'],
                    ];

                    $list[$value['link']] = $data;
                }


            }

        }

        foreach (static::$modules as $key => $value) {


            if (in_array($user_permission, $value['access_permission'])) {

                if ($value['type'] == "child") {

                    if (!empty($value['type'])) {
                        $data = [
                            "name" => $value['name'],
                            "link" => $value['link'],
                            "icon" => "",
                            "show" => $value['show']
                        ];
                    }

                    $list[$value['parent']]["child"][] = $data;
                }


            }

        }

        \Helper::sort_array($list, "position");

        return $list;
    }


    public function get_modules_data($link)
    {
        if (!empty(static::$modules[$link])) {

            return static::$modules[$link];

        } else {
            if ($link == $this->config["pages"]->panel) {
                header("LOCATION: /panel/home");
                // return [
                //     "link" => "",
                //     "name" => "",
                //     "function" => "home",
                //     "access_permission" => [1]
                // ];
            }
            return [
                "link" => "",
                "name" => "",
                "function" => "",
                "access_permission" => ""
            ];

        }
    }

    public function get_module($name)
    {
        if (!empty($this->get_modules_data($name)["function"])) {
            $output = call_user_func($this->get_modules_data($name)["function"]);
        } else {
            $output = "";
        }
        return $output;

    }

}
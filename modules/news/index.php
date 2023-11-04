<?php

namespace News;

// Interface news
require_once __DIR__ . "/interface/interface-api-news.php";
require_once __DIR__ . "/interface/interface-page-news.php";

// traits news
require_once __DIR__ . "/traits/trait-news-page.php";
require_once __DIR__ . "/traits/trait-news-api.php";



class News
{

    use TNewsPages, TNewsApi, \ModuleManager\LoadFile;

    private int $user_id = 0;

    public function __construct()
    {
        $token = \ModuleManager\LocalStorage::get_data("token", 'session', true);
        if (empty($token)) {
            $token = \ModuleManager\LocalStorage::get_data("token", 'cookie', true);
        } else {
            $this->user_id = \ModuleManager\Main::$jwt->check_token($token)['payload']->user_id;
        }

    }

    // news

    public function init_update_pages()
    {

        $first_page = [
            "name" => "Aktualności",
            "link" => "news",
            "function" => [$this, "get_news_list"],
            "permission" => [11],
            "position" => 6,
            "status" => true,
            "icon" => basename(__DIR__) . "/assets/img/test.svg"
        ];

        \ModuleManager\Pages::set_modules($first_page);

        // $first_page = [
        //     "name" => "Dodaj aktualności",
        //     "link" => "add_news",
        //     "parent_link" => "news",
        //     "function" => [$this, "add_news"],
        //     "permission" => [1],
        //     "status" => true,
        //     "icon" => basename(__DIR__) . "/assets/img/test.svg"
        // ];

        // \ModuleManager\Pages::set_child_modules($first_page);

        $first_page = [
            "name" => "Edytuj aktualności",
            "link" => "update_news",
            "parent_link" => "news",
            "function" => [$this, "update_news"],
            "permission" => [1],
            "status" => true,
            "show" => false,
            "icon" => basename(__DIR__) . "/assets/img/test.svg"
        ];

        \ModuleManager\Pages::set_child_modules($first_page);

        $first_page = [
            "name" => "Dodaj nową",
            "link" => "add_news",
            "parent_link" => "news",
            "function" => [$this, "add_news"],
            "permission" => [1],
            "status" => true,
            "show" => true,
            "icon" => basename(__DIR__) . "/assets/img/test.svg"
        ];

        \ModuleManager\Pages::set_child_modules($first_page);

        $first_page = [
            "name" => "Dodaj nową 2",
            "link" => "add_news2",
            "parent_link" => "news",
            "function" => [$this, "add_news"],
            "permission" => [1],
            "status" => true,
            "show" => true,
            "icon" => basename(__DIR__) . "/assets/img/test.svg"
        ];

        \ModuleManager\Pages::set_child_modules($first_page);

        $first_page = [
            "name" => "Dodaj nową 3",
            "link" => "add_news3",
            "parent_link" => "news",
            "function" => [$this, "add_news"],
            "permission" => [1],
            "status" => true,
            "show" => true,
            "icon" => basename(__DIR__) . "/assets/img/test.svg"
        ];

        \ModuleManager\Pages::set_child_modules($first_page);

        $first_page = [
            "name" => "Edytuj",
            "link" => "edit_news",
            "parent_link" => "news",
            "function" => [$this, "edit_news"],
            "permission" => [1],
            "status" => true,
            "show" => false
        ];

        \ModuleManager\Pages::set_child_modules($first_page);




    }

    public function init_update_api()
    {
        $news_api = [
            "link" => 'news',
            "function" => [$this, 'get_news'],
            "http_methods" => "GET",
            "permission" => [0]
        ];
        \ModuleManager\Pages::set_endpoint($news_api);

        $news_details_api = [
            "link" => 'news_details',
            "function" => [$this, 'get_news_details'],
            "http_methods" => "GET",
            "permission" => [0]
        ];
        \ModuleManager\Pages::set_endpoint($news_details_api);

    }

    public function init_binder()
    {

        \ModuleManager\DataBinder::set_binder(
            [
                "key" => "page_generate_data",
                "function" => [$this, "page_generate_data"]
            ]
        );

    }

}

$update = new News;
$update->init_update_pages();
$update->init_update_api();
$update->init_binder();
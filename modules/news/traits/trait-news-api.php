<?php

namespace News;

trait TNewsApi
{

    public function get_news(array $input): array
    {
        global $main;

        $output = [];

        $news_list = $main->sedjm->get([
            'news_id',
            'title',
            'short_description',
            'key_words',
            'article',
            'type',
            'distinctive_image',
            [
                "table" => 'News',
                "column" => 'add_date',
                "alias" => 'add_date',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => 'News',
                "column" => 'update_date',
                "alias" => 'update_date',
                "function" => ["Helper", "time_to_data"]
            ]
        ], 'News');

        foreach ($news_list as $key => $value) {
            $output[] = [
                "id" => $value['news_id'],
                "title" => $value['title'],
                "excerpt" => $value['short_description'],
                // "image" => "",
                "image" => $value['distinctive_image'],
                "date" => $value['add_date'],
                "update_date" => $value['update_date'],
            ];
        }



        return ['data' => $output];
    }

    public function get_news_details(array $input): array
    {
        global $main;

        $main->sedjm->set_where('news_id', $input['id'], '=');
        $news_list = $main->sedjm->get([
            'news_id',
            'title',
            'short_description',
            'key_words',
            'article',
            'type',
            'distinctive_image',
            [
                "table" => 'News',
                "column" => 'add_date',
                "alias" => 'add_date',
                "function" => ["Helper", "time_to_data"]
            ],
            [
                "table" => 'News',
                "column" => 'update_date',
                "alias" => 'update_date',
                "function" => ["Helper", "time_to_data"]
            ]
        ], 'News');


        $article = str_replace("&amp;quot;", "\"", $news_list[0]['article']);
        $article = json_decode($article, true);

        $output['data'] = [
            "id" => $input['id'],
            "title" => $news_list[0]['title'],
            "excerpt" => $news_list[0]['short_description'],
            "image" => $news_list[0]["distinctive_image"],
            "date" => $news_list[0]['add_date'],
            "content" => $article
        ];

        return $output;
    }

}
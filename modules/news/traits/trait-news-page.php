<?php

namespace News;

trait TNewsPages
{
    private array $actual_post_data;
    public function get_news_list(): string
    {
        global $main;
        if (!empty($_GET['type']) && $_GET['type'] == 'delete' && !empty($_GET['id'])) {
            try {

                $main->sedjm->clear_all();
                $main->sedjm->set_where("news_id", $_GET['id'], '=');
                $main->sedjm->delete("News");
                $main->sedjm->clear_all();

                $main->popups->add_popup("Usunięto aktualność", "", "correct");
                $main->popups->show_popups();
            } catch (\Throwable $th) {

            }
        }

        $news = $this->get_news([])['data'];
        $table = new \ModuleManager\Table(50);
        $news_header = [
            "Tytuł" => ["title"],
            "Skrócony opis" => ["excerpt"],
            "Data utworzenia" => ["date"],
            "Data aktualizacji" => ["update_date"]
        ];
        $table->set_action('../news', 'delete');
        $table->set_action('edit_news', 'edit');
        $table->set_id("id");
        return $table->generate_table($news, $news_header);
    }

    public function add_news(): string
    {
        $id = 0;
        global $main;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                !empty($_POST['title'])
            ) {

                $content = !empty($_POST['content']) ? $_POST['content'] : "";
                $title = !empty($_POST['title']) ? $_POST['title'] : "";
                $short_description = !empty($_POST['short_description']) ? $_POST['short_description'] : "";
                $keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : "";
                $distinctive_image = !empty($_POST['distinctive_image']) ? $_POST['distinctive_image'] : "";

                $new_post = $this->save_news($content, $title, $short_description, $keywords, $distinctive_image);

                if ($new_post['status']) {
                    header("LOCATION: edit_news?id=" . $new_post['id'] . "&alert=true");
                }

            } else {
                $main->popups->add_popup("Nie podano tytułu", "", "error");
                $main->popups->show_popups();

                $this->actual_post_data = [
                    'title' => !empty($_POST['title']) ? $_POST['title'] : "",
                    'short_description' => !empty($_POST['short_description']) ? $_POST['short_description'] : "",
                    'key_words' => !empty($_POST['keywords']) ? $_POST['keywords'] : "",
                    'article' => !empty($_POST['content']) ? $_POST['content'] : "",
                    'distinctive_image' => !empty($_POST['distinctive_image']) ? $_POST['distinctive_image'] : ""
                ];

            }


        }
        $link = __DIR__ . "/../assets/html/page.html";
        return $this->get_page($link);

    }

    public function edit_news(): string
    {

        global $main;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (
                !empty($_POST['title'])
            ) {

                $content = !empty($_POST['content']) ? $_POST['content'] : "";
                $title = !empty($_POST['title']) ? $_POST['title'] : "";
                $short_description = !empty($_POST['short_description']) ? $_POST['short_description'] : "";
                $keywords = !empty($_POST['keywords']) ? $_POST['keywords'] : "";
                $distinctive_image = !empty($_POST['distinctive_image']) ? $_POST['distinctive_image'] : "";


                $new_post = $this->update_news($content, $title, $short_description, $keywords, $_GET['id'], $distinctive_image);

                if ($new_post['status']) {
                    $main->popups->add_popup("Zaktualizowano wpis", "", "correct");
                    $main->popups->show_popups();
                }

            }

        }

        if (!empty($_GET['alert']) && $_GET['alert'] == "true") {
            $main->popups->add_popup("Dodano wpis", "", "correct");
            $main->popups->show_popups();
        }

        $this->actual_post_data = $this->get_post_data($_GET['id']);

        $link = __DIR__ . "/../assets/html/page.html";
        return $this->get_page($link);

    }

    // private 

    private function save_news($content, $title, $short_description, $key_words, $distinctive_image)
    {
        global $main;

        $data = [
            "title" => $title,
            "user_id" => $this->user_id,
            "short_description" => $short_description,
            "key_words" => $key_words,
            "article" => $content,
            "type" => "",
            "update_date" => time(),
            "add_date" => time(),
            "distinctive_image" => $distinctive_image
        ];

        $x = $main->sedjm->insert($data, "News");
        return $x;
    }

    private function update_news($content, $title, $short_description, $key_words, $id, $distinctive_image)
    {
        global $main;

        $data = [
            "title" => $title,
            "short_description" => $short_description,
            "key_words" => $key_words,
            "article" => $content,
            "type" => "",
            "update_date" => time(),
            "distinctive_image" => $distinctive_image
        ];

        $main->sedjm->clear_all();
        $main->sedjm->set_where('news_id', $id, '=');
        $x = $main->sedjm->update($data, "News");
        return $x;
    }

    private function get_post_data($id)
    {
        global $main;

        $main->sedjm->clear_all();
        $main->sedjm->set_where("news_id", $id, "=");
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

        return $news_list[0];
    }


    private function page_generate_data($value)
    {
        // var_dump($this->actual_post_data['short_description']);
        // if (!empty($_GET['id'])) {



        switch ($value[0]) {
            case 'title':
                return !empty($this->actual_post_data['title']) ? $this->actual_post_data['title'] : "";
            case 'short_description':
                return !empty($this->actual_post_data['short_description']) ? $this->actual_post_data['short_description'] : "";
            case 'key_words':
                return !empty($this->actual_post_data['key_words']) ? $this->actual_post_data['key_words'] : "";
            case 'distinctive_image':
                return !empty($this->actual_post_data['distinctive_image']) ? $this->actual_post_data['distinctive_image'] : "";
            case 'distinctive_image_img':
                if (!empty($this->actual_post_data['short_description'])) {

                    $img = "<img src='" . $this->actual_post_data['distinctive_image'] . "' />";
                    return $img;
                } else {
                    return "";
                }
            case 'article':
                if (!empty($this->actual_post_data['article'])) {
                    $article = str_replace("&amp;quot;", "\"", $this->actual_post_data['article']);
                    return $article;
                } else {
                    return "";
                }
            default:
                return "";
        }

        // } else {
        //     return "";
        // }
    }

}
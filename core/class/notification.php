<?php

namespace ModuleManager;

class Popups
{

    private array $info;

    public function add_popup($title, $content, $status)
    {

        $this->info[] = [
            "title" => $title,
            "content" => $content,
            "status" => $status
        ];

    }

    public function show_popups()
    {
        $html = "";
        if (!empty($this->info)) {

            $html = "<div class='popup-box'>";

            foreach ($this->info as $key => $value) {
                $html .= $this->get_popup_format($value['title'], $value['content'], $value['status']);
            }

            $html .= "</div>";

            echo $html;
        }

    }

    private function get_popup_format($title, $content, $status): string
    {

        $html = "<div class='popup " . $status . "'>";
        switch ($status) {
            case 'correct':
                $html .= "<div class='icon'><img src='/panel-template/img/correct.svg' alt='correct'></div>";
                break;
            case 'error':
                $html .= "<div class='icon'><img src='/panel-template/img/error.svg' alt='correct'></div>";
                break;

            default:
                # code...
                break;
        }

        $html .= "<div class='content'>";
        $html .= "<p class='title'>" . $title . "</p>";
        $html .= "<p class='description'>" . $content . "</p>";
        $html .= "</div>";
        $html .= "<div class='close'><img src='/panel-template/img/close.svg' alt='correct'></div>";
        $html .= "</div>";

        return $html;

    }
}
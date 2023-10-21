<?php

namespace News;

interface INewsPages
{
    public function get_news_list(): string;
    public function add_news(): string;

}
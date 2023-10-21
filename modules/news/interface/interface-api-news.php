<?php

namespace News;

interface INewsApi
{
    public function get_news(array $input): array;
    public function get_news_details(array $input): array;

}
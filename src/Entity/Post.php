<?php

    namespace App\Entity;

    use App\DB\Entity;

    class Post extends Entity
    {
        protected $table = 'posts';
        static $filters = [
            'title' => FILTER_SANITIZE_STRING,
            'content' => FILTER_SANITIZE_STRING,
            'category_id' => FILTER_SANITIZE_NUMBER_INT
        ];

        static function slugify($text)
        {
            $text = preg_replace('~[^\pL\d]+~u', '-', $text);
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
            $text = preg_replace('~[^-\w]+~', '', $text);
            $text = trim($text, '-');
            $text = preg_replace('~-+~', '-', $text);
            $text = strtolower($text);
            if (empty($text)) {
                return 'n-a';
            }
            return $text;
        }
    }
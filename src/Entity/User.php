<?php

    namespace App\Entity;

    use App\DB\Entity;

    class User extends Entity
    {
        protected $table = 'users';
        static $filters = [
            'first_name' => FILTER_SANITIZE_STRING,
            'last_name' => FILTER_SANITIZE_STRING,
            'email' => FILTER_SANITIZE_STRING,
            'username' => FILTER_SANITIZE_STRING,
            'password' => FILTER_SANITIZE_STRING,
            'password_confirm' => FILTER_SANITIZE_STRING
        ];
    }
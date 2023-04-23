<?php

namespace App\DB;

class Connection
{   
    private static $instance = null;
    private function __construct()
    {
    }

    public static function getInstance()
    {   
        if(is_null(self::$instance)){
            self::$instance = new \PDO('mysql:dbname=blog;host=localhost','root','');
            self::$instance->exec('SET NAMES UTF8');
        }
        return self::$instance;
    }
}
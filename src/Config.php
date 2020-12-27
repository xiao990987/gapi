<?php
namespace gapi;

class Config{

    public static array $config = [];

    public static function file(string $file = 'config'):mixed
    {
        $config_file = Loader::file(file:$file,flag:true);
        if(!isset(self::$config[$config_file])){
            self::$config[$config_file] = Loader::file(file:$file);
        }
        return self::$config[$config_file];
    }

    public static function get($key){



    }

    public static function set($key,$value){



    }

}
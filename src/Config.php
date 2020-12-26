<?php
namespace gapi;

class Config{

    public static function file(string $file = 'config'):mixed
    {
        return Autoload::file($file);
    }

    public static function get($key){



    }

    public static function set($key,$value){



    }

}
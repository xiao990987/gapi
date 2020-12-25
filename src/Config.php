<?php
namespace gapi;

class Config{

    public static function file($file = 'config')
    {
        $file = VERSION_PATH . DS . $file;
        if (file_exists($file)) {
            return include $file;
        }
        return [];
    }

    public static function get($key){



    }

    public static function set($key,$value){



    }

}
<?php

namespace gapi;

use gapi\lib\Logger;

class Autoload
{


    public static function init():void
    {
        # app
        spl_autoload_register(['\\gapi\\Autoload', 'app']);
    }


    # app mvc
    private static function app(String $class):void
    {

        $class = substr($class, 4);
        $class_file = VERSION_PATH . DS . str_replace('\\', DS, $class) . '.php';
        if (file_exists($class_file)) {
            require $class_file;
            Logger::info('加载文件:' . $class_file);
        }
    }

}
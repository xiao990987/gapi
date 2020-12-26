<?php

namespace gapi;

use gapi\lib\Logger;

class Autoload
{


    public static function init(): void
    {
        # app
        spl_autoload_register(['\\gapi\\Autoload', 'app']);
    }

    # app mvc
    private static function app(string $class): void
    {
        $class = substr($class, 4);
        $file = str_replace('\\', DS, $class) . '.php';
        $class_file = VERSION_PATH . DS . $file;
        //当前版本
        if (file_exists($class_file)) {
            require $class_file;
            Logger::info('APP 加载文件:' . $class_file);
            return;
        }
        //回溯上一个版本
        foreach (self::version() as $version){
            $class_file = APP_PATH.DS.$version.DS.$file;
            if (file_exists($class_file)) {
                require $class_file;
                Logger::info('APP 加载文件:' . $class_file);
                return;
            }
        }
    }


    # 获取版本列表
    public static function version(string $current = ''): array
    {
        $version_list = dir_list_one(APP_PATH . DS);
        sort($version_list);
        $versions = [];
        foreach ($version_list as $version) {
            if($version==$current){
                break;
            }
            $versions[] = $version;
        }
        return array_reverse($versions);
    }

    public static function file($file):mixed
    {
        $version_file = VERSION_PATH . DS . $file;
        //当前版本
        if (file_exists($version_file)) {
            return include $version_file;
        }
        //回溯上一个版本
        foreach (Autoload::version() as $version){
            $version_file = APP_PATH.DS.$version.DS.$file;
            if (file_exists($version_file)) {
                return include $version_file;
            }
        }
        return [];
    }

}
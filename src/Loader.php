<?php

namespace gapi;

use gapi\lib\Logger;

class Loader
{
    public static function autoload(): void
    {
        # app
        spl_autoload_register(['\\gapi\\Loader', 'app']);
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
        foreach (self::version() as $version) {
            $class_file = APP_PATH . DS . $version . DS . $file;
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
            if ($version == $current) {
                break;
            }
            $versions[] = $version;
        }
        return array_reverse($versions);
    }

    public static function system(string $file): array
    {
        $file = ROOT_PATH . DS . 'config' .DS. $file;
        if (file_exists($file)) {
            return include $file;
        }
        return [];
    }

    public static function file(string $file, string $version = '',bool $flag=false): mixed
    {
        if (defined('VERSION_PATH')) {
            $version = $version == '' ? VERSION_PATH : $version;
        }

        $version_file = $version . DS . $file;
        //当前版本
        if (file_exists($version_file)) {
            if($flag) return $version_file;
            return include $version_file;
        }
        //回溯上一个版本
        foreach (Loader::version() as $version) {
            $version_file = APP_PATH . DS . $version . DS . $file;
            if (file_exists($version_file)) {
                if($flag) return $version_file;
                return include $version_file;
            }
        }
        if($flag) return '';
        return [];
    }


    public static function controllers(string $current_version = ''): array
    {
        $versions = array_merge([$current_version], self::version($current_version));
        $versions = array_reverse($versions);
        $modules = Loader::file('module.php');
        $controllers = [];
        foreach ($modules as $module) {
            foreach ($versions as $version) {
                //获取控制器目录
                $module_path = APP_PATH . DS . $version . DS . $module . DS . 'controller';
                //获取控制器列表
                $files = dir_list($module_path);
                foreach ($files as $file) {
                    $controllers[$module . '/' . substr(basename($file), 0, -4)] = $file;
                }
            }
        }
        //sort($controllers);

        return $controllers;
    }


}
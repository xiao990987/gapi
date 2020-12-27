<?php

namespace gapi\command;

use gapi\lib\Logger;
use gapi\Loader;

class Route
{

    public static function execute($params, $output)
    {
        echo "============生成路由规则===========\n";

//        foreach(Loader::version() as $version){
//            self::update($version);
//        }
        echo APP_PATH;
        echo "\n\n";
        $versions = Loader::version();
        print_r($versions);
        $version = $params[0];
        if(in_array($version,$versions)){
            self::update($version);
        }

    }


    public static function update($version): void
    {
        $content = '<?php
namespace app;
use gapi\Route;

';
        $controllers = Loader::controllers($version);
        foreach ($controllers as $controller => $file) {
            //手动加载控制器文件
            if (file_exists($file)) {
                include $file;
                Logger::info($file);
                $class = '\\app\\' . implode('\\controller\\', explode('/', $controller));
                //获取路由注解
                $routes = self::controller($class);
                //创建路由规则
                self::create($routes,$content);
            }
        }

        $file = APP_PATH.DS.$version.DS.'router.php';
        file_put_contents($file,$content);
        echo "{$content}\n{$file} 生成成功\n";

    }

    /**
     * 获取控制器路由注解
     * @param string $controller
     * @return array
     * @throws \ReflectionException
     */
    public static function controller(string $controller): array
    {
        $controller = new \ReflectionClass($controller);
        $methods = $controller->getMethods(\ReflectionMethod::IS_PUBLIC);

        $routes = [];
        foreach ($methods as $method) {
            $attributes = $method->getAttributes(\gapi\Route::class);
            foreach ($attributes as $attribute) {
                $routes[] = $attribute->newInstance()->setHandler($method);
            }
        }
        return $routes;
    }


    public static function create($routes,&$content){
        foreach($routes as $route){
            $class = explode('\\',$route->handler->class);
            $action = $class[1].'/'.$class[3].'/'.$route->handler->name;
            $pattern = [];
            if($route->pattern){
                foreach ($route->pattern as $name=>$p){
                    $pattern[] = "'{$name}'=>'{$p}'";
                }
            }
            $methods = explode('|',$route->methods);
            foreach($methods as $method){
                $content .= 'Route::'.$method.'([\''.implode("','",$route->path).'\'],\''.$action.'\',['.implode(',',$pattern).']);'."\n";
            }
        }
    }
}
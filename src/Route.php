<?php

namespace gapi;
use gapi\lib\Logger;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION | \Attribute::IS_REPEATABLE)]
class Route
{
    public static string $version = '';
    public static string $flag = 's';

    /**
     * 存储路由
     * 如果当前请求类型不存在会自动向ALL中查找
     * @var array
     */
    public static array $routeLists = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTIONS' => [],
        'HEAD' => [],
        'ALL' => [],
    ];
    public $handler;

    public function __construct(
        public string|array $path = [],
        public string $methods = '',
        public string|array $pattern = [],
    )
    {
    }

    public function setHandler($handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function run(): void
    {
        call_user_func([new $this->handler->class, $this->handler->name]);
    }

    public function send(?array $params = []): void
    {

        if ($params) {
            $method = $params['method'];
            if (method_exists(self::class, $method)) {
                Route::$method($params['path'], $params['action'], isset($params['pattern']) ? $params['pattern'] : []);
            } else {
                throw new \Exception("ROUTE::{$method} 不存在");
            }
        } else {
            Loader::file('router.php');
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return Router
     */
    public static function __callStatic(string $method, array $params = []): static
    {
       return new static($method, isset($params['path']) ?? $params['path'], isset($params['callback']) ?? $params['callback']);
    }

    public static function uri(): string
    {

        $uri = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        if ($uri == '') return '/';
        $uri_params = explode('&', $uri);
        $params = [];
        if ($uri_params) {
            foreach ($uri_params as $v) {
                $tmp = explode('=', $v);
                $params[$tmp[0]] = $tmp[1];
            }
        }
        return isset($params[self::$flag]) ? '/' . $params[self::$flag] : '/';
    }


    public static function all(string $type, array $paths, string|callable $action, array $pattern = []): void
    {
        $route = '';
        $params = [];
        $uri = self::uri();

        if ($pattern) {
            foreach ($paths as $path) {
                $params = self::matchPattern($path, $pattern, $uri);
                if ($params) {
                    $route = $action;
                    break;
                }
            }
        } else {
            foreach ($paths as $v) {
                if ($uri == $v) {
                    $route = $action;
                    break;
                }
            }
        }

        if (is_callable($route)) {
            $route($params);
        } else {
            self::runRoute($route, $params);
        }


    }

    public static function get(array $paths, string|callable $action, array $pattern = []): void
    {
        $route = '';
        $params = [];
        $uri = self::uri();
        if ($pattern) {
            foreach ($paths as $path) {
                $params = self::matchPattern($path, $pattern, $uri);
                if ($params) {
                    $route = $action;
                    break;
                }
            }
        } else {
            foreach ($paths as $v) {
                if ($uri == $v) {
                    $route = $action;
                    break;
                }
            }
        }
        if (is_callable($route)) {
            $route($params);
        } else {
            self::runRoute($route, $params);

        }

    }

    public static function post(array $paths, string|callable $action, array $pattern = []): void
    {
        self::all('post', $paths, $action, $pattern);
    }

    public static function request(array $paths, string|callable $action, array $pattern = []): void
    {
        self::all('request', $paths, $action, $pattern);
    }


    public static function matchPattern(string $path, array $pattern, string $uri): array
    {
        $path = str_replace('/', '\/', $path);
        foreach ($pattern as $name => $value) {
            $path = str_replace('{' . $name . '}', '(' . $value . ')', $path);
        }
        preg_match_all('/^' . $path . '$/i', $uri, $params);

        $routes = [];
        if ($params[0]) {
            $i = 0;
            foreach ($pattern as $name => $value) {
                $routes[$name] = $params[++$i][0];
            }
        }
        return $routes;
    }

    public static function runRoute(string $route, array $params = []): void
    {
        if ($route == '') return;
        $mvc = explode('/', $route);
        $action = $mvc[2];
        $controller = $mvc[1];
        $module = $mvc[0];
        $class = "\\app\\{$module}\\controller\\" . ucfirst($controller);

        if (class_exists($class)) {
            $controller = new $class();
            define('ACTION_NAME', $mvc[2]);
            define('CONTROLLER_NAME', $mvc[1]);
            define('MODULE_NAME', $mvc[0]);
            $controller->$action($params);
            Logger::info("当前执行方法: {$class}@{$action}");
            Logger::info("消耗内存 " . Debug::getUseMem());
            Logger::info("耗时 " . Debug::getUseTime() . ' 秒');
            Logger::info("吞吐率 " . Debug::getThroughputRate());
            Logger::info("共运行 " . Debug::getFile() . " 个文件");
            Logger::info("\n" . implode("\n", Debug::getFile(true)));
            exit;
        }

    }


}

<?php
declare(strict_types=1);

namespace gapi;

use gapi\lib\Logger;

date_default_timezone_set('PRC');
define('RUN_START_TIME', microtime(true));
define('RUN_START_MEM', memory_get_usage());
define('CORE_PATH', dirname(__FILE__));
define('ROOT_PATH', dirname(CORE_PATH));
define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH . DS . 'app');
require ROOT_PATH . DS . 'vendor/autoload.php';


class Application
{
    private $name = 'GAPI';

    public function __construct(
        public string $path = '',
        public string $version = 'v1.0.0',
        public array $config = [],
    )
    {


        $this->version = Request::get('v', '');
        $this->version = $this->version == '' ? Loader::system('config.php')['app_version'] : 'v' . $this->version;
        if (!defined('APP_VERSION')) {
            define('APP_VERSION', $this->version);
        }
        $this->path = $this->path == '' ? $this->version : $this->path;
        if (!defined('VERSION_PATH')) {
            define('VERSION_PATH', APP_PATH . DS . $this->path);
        }
        if (!file_exists(VERSION_PATH)) {
            throw new \Exception(VERSION_PATH . ' 目录不存在.');
        }
        Logger::info("当前版本：version {$this->version} - {$this->name}");
        self::runtimeCache();
    }

    /**
     * 创建请求
     * @return $this
     */
    public function create(): self
    {
        $this->route = new Route();

        //加载配置
        Config::file(VERSION_PATH . DS . 'config.php');

        return $this;
    }

    /**
     * 发送请求
     */
    public function send(?array $params = []): void
    {
        Loader::autoload();
        $this->route->send($params);
        Logger::info("消耗内存 " . Debug::getUseMem());
        Logger::info("耗时 " . Debug::getUseTime() . ' 秒');
        Logger::info("吞吐率 " . Debug::getThroughputRate());
        Logger::info("共运行 " . Debug::getFile() . " 个文件");
        Logger::info("\n" . implode("\n", Debug::getFile(true)));
    }

    public static function runtimeCache(): void
    {
        if (!defined('RUNTIME_PATH')) {
            define('RUNTIME_PATH', VERSION_PATH . DS . 'runtime');
        }


        if (!file_exists(RUNTIME_PATH)) {
            mkdir(RUNTIME_PATH, 0777, true);
        }
    }


}
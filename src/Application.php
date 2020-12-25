<?php
declare(strict_types=1);
namespace gapi;
use gapi\lib\Logger;

date_default_timezone_set('PRC');


define('CORE_PATH', dirname(__FILE__));
define('ROOT_PATH', dirname(CORE_PATH));
define('DS', DIRECTORY_SEPARATOR);
define('APP_PATH', ROOT_PATH.DS.'app');
require  ROOT_PATH.DS.'vendor/autoload.php';


class Application
{
    private $name='GAPI';

    public function __construct(
        public string $path = '',
        public string $version = '1.0.0',
        public array $config = [],
    )
    {
        $this->version = Request::get('v','1.0.0');
        $this->path = $this->path==''? 'v'.$this->version : $this->path;
        define('VERSION_PATH',APP_PATH.DS.$this->path);
        if(!file_exists(VERSION_PATH)){
            throw new \Exception(VERSION_PATH.' 目录不存在.');
        }
        Logger::info("当前版本：version {$this->version} - {$this->name}");
        self::runtimeCache();
    }

    /**
     * 创建请求
     * @return $this
     */
    public function create() :self
    {
        Autoload::init();
        $this->route = new Route();

        //加载配置
        Config::file(VERSION_PATH.DS.'config.php');

        return $this;
    }

    /**
     * 发送请求
     */
    public function send(?array $params=[]):void
    {

        $this->route->send(array_merge($params,['version'=>$this->version]));
    }

    public static function runtimeCache():void
    {

        define('RUNTIME_PATH', VERSION_PATH . DS . 'runtime');

        if (!file_exists(RUNTIME_PATH)) {
            mkdir(RUNTIME_PATH, 0777, true);
        }
    }


}
<?php

namespace gapi\lib;

use gapi\Config;

class Logger
{

    static $path = ROOT_PATH . DS . 'data/logs';
    static $format = 'Y-m-d';
    static $suffix = '.log';
    static $level = 'info,debug,error';// info,debug,error
    static $levels = ['info', 'debug', 'error'];
    static $wss = 1; # 开启websocket 调试  默认地址：wss://127.0.0.1:8866/wss
    static $open = 1; # 是否开启日志

    static $logfile = false;
    static $debug = false;
    static $logIndex = 0;


    public static function write(string $msg,string $type = 'info'):void
    {
        if(++self::$logIndex){
            self::$debug = Config::file('config.php')['debug'];
            self::$logfile = Config::file('config.php')['logfile'];
        }
        if(!self::$debug){
            return ;
        }

        if (self::$open == 0) {
            return;
        }

        if ($msg == '') {
            return;
        }
        # 只记录配置的类型
        if (!in_array($type, explode(',', self::$level))) {
            return;
        }

        $info = "[{$type}]" . date('m-d H:i:s: ') . $msg . "\n";
        if (self::$wss) {
            # 同步日志服务
            \gapi\extend\WebSocketClient::getInstance()->sendData($info);
        }


        if(self::$logfile){
            if (!file_exists(self::$path)) {
                @mkdir(self::$path, 0777, 1);
            }
            $file = self::$path . DS . date(self::$format) . self::$suffix;
            $fp = fopen($file, "a+");
            fwrite($fp, $info);
            fclose($fp);
        }

    }

    public static function error(string $msg):void{
        self::write($msg,'error');
    }

    public static function info(string $msg):void{
        self::write($msg,'info');
    }

    public static function debug(string $msg):void{
        self::write($msg,'debug');
    }




}
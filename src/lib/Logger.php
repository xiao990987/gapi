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
    static $wss = false; # 开启websocket 调试  默认地址：wss://127.0.0.1:8866/wss

    static $logfile = false;
    static $debug = false;
    static $logIndex = 0;
    static $trace = false;


    public static function write(string $msg,string $type = 'info'):void
    {
        if(++self::$logIndex){
            $config = Config::file('config.php');
            self::$debug = $config['debug'];
            self::$logfile = $config['logfile'];
            self::$wss = $config['wss'];
            self::$trace = $config['trace'];
        }
        if(self::$trace){
            echo $msg;
            return ;
        }


        if(!self::$debug){
            return ;
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
<?php

namespace gapi\command;

use gapi\lib\Logger;
use gapi\Loader;

class Auto
{

    public static function execute($params, $output)
    {
        echo "============自动任务===========\n";
        $type = $params[0];
        if($type == 'route'){
            foreach(Loader::version() as $version){
                self::route($version);
            }
        }
    }

    public static function route($version){
       // header('Content-Type: text/html; charset=gbk');
        $cmd = "php build route {$version}";
        #$pipes = array();
        $out1 = "";
        system($cmd,$out1);
        // iconv('gb2312','utf-8',$out1);
    }

}
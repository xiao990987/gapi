<?php
namespace gapi\command;

use gapi\extend\WebSocketServer;

class Server{

    public static function execute($params,$output){
        $sock = new WebSocketServer();
        $sock->run('127.0.0.1',8877);
    }

}
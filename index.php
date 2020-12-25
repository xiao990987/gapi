<?php
require 'src/Application.php';
//(new \gapi\Application())->create()->send();
//(new \gapi\Application(path: 'v1.0.0'))->create()->send(
//    [
//        'method'=>'get',
//        'path'=>['/hello/{num}'],
//        'action' => 'home/index/hello',
//        'pattern'=>['num'=>'\d+']
//    ]
//);

(new \gapi\Application(path: 'v1.0.0'))->create()->send(
    [
        'method'=>'get',
        'path'=>['/hello/{num}'],
        'action' => function($route){
            print_r($route);
            echo 'hello world';

         },
        'pattern'=>['num'=>'\d+']
    ]
);


<?php

namespace app\home\controller;
use gapi\Config;
use gapi\Request;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Index
{
    #[\gapi\Route(path: "/index", methods: "get")]
    public function index()
    {

        $databaseConfig = Config::file('database.php');



        print_r($databaseConfig);

    }

    #[\gapi\Route(path: "/next", methods: "get")]
    public function next()
    {
        echo 'next';
    }


    #[\gapi\Route(path: "/num/{num}/{str}", methods: "get",pattern:['num'=>'\d+','str'=>'.+'])]
    public function nums(?array $route): void
    {
        print_r($route);
        echo 'nums';
    }

    #[\gapi\Route(path: "/hello", methods: "get")]
    public function hello(?array $route): void
    {
        echo 'hello';
        print_r($route);
        print_r(Request::get());
    }


}
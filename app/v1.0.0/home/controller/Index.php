<?php

namespace app\home\controller;
use gapi\Autoload;
use gapi\Config;
use gapi\database\Db;
use gapi\database\Query;
use gapi\Request;
use gapi\Route;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Index
{
    #[\gapi\Route(path: ["/index","/"], methods: "get")]
    public function index()
    {

        $query = Db::connect(Config::file('database.php'))->query("SHOW COLUMNS from lg_member;");

        $queryObj = new Query();
        echo $queryObj->version();
        $queryObj->table('lg_member');
        print_r($query);

    }

    #[\gapi\Route(path: ["/next"], methods: "get")]
    public function next()
    {
        echo 'next';
    }


    #[\gapi\Route(path: ["/num/{num}/{str}"], methods: "get",pattern:['num'=>'\d+','str'=>'.+'])]
    public function nums(?array $route): void
    {
        print_r($route);
        echo 'nums';
    }

    #[\gapi\Route(path: ["/hello"], methods: "get")]
    public function hello(?array $route): void
    {
        echo 'hello';
        print_r($route);
        print_r(Request::get());
    }


}
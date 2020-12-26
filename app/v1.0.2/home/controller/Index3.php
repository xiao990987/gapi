<?php

namespace app\home\controller;
use gapi\Autoload;
use gapi\Config;
use gapi\database\Db;
use gapi\database\Query;
use gapi\Loader;
use gapi\Request;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Index3
{
    #[\gapi\Route(path: ["/index3"], methods: "get")]
    public function index()
    {


        $query = Db::connect(Config::file('database.php'))->query("SHOW COLUMNS from lg_member;");

        $queryObj = new Query();
        echo $queryObj->version();
        $queryObj->table('lg_member');
        print_r($query);

    }




}
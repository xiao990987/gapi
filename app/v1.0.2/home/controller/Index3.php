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

        $db = Db::connect(Config::file('database.php'));

        $queryObj = new Query();
        $queryObj->from('managers','lg_')->field('*')->limit(10)->order('uid desc')->select();
        $lists = $db->query($queryObj);

        print_r($lists);

    }




}
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

        //demo 1
        $lists = $db->query(
            (new Query())->from('managers', 'lg_')->field('*')->order('uid desc')->find()
        );

        print_r($lists);


        echo "\n\n";

        //demo 2
        $lists = $db->query(
            (new Query())->from('managers', 'lg_')->max('uid', 'uid')->order('uid desc')->find()
        );


        print_r($lists);


    }


}
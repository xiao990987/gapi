<?php
namespace gapi;

class Request{

    private $filterTypes=[
        'string',
        'array',
        'int',
        'float',
        'number',
        'bool',
    ];



    public static function get(?string $key='',?string $default='',?string $filter=''):string|array
    {
        if($key==''){
            return $_GET;
        }
        $params = '';
        if(isset($_GET[$key])){
            $params = $_GET[$key];
        }
        $params = $params? $params: $default;
        return $params;
    }

    public static function post(?string $key='',?string $default='',?string $filter=''):string|array
    {
        if($key==''){
            return $_POST;
        }
        $params = '';
        if(isset($_POST[$key])){
            $params = $_POST[$key];
            $params = $params? $params: $default;
        }
        return $params;
    }

}
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

    public static function request(?string $key='',?string $default='',?string $filter=''):string|array
    {
        if($key==''){
            return $_REQUEST;
        }
        $params = '';
        if(isset($_REQUEST[$key])){
            $params = $_REQUEST[$key];
        }
        $params = $params? $params: $default;
        return $params;
    }

    public static function files(?string $key='',?string $default='',?string $filter=''):string|array
    {
        if($key==''){
            return $_FILES;
        }
        $params = '';
        if(isset($_FILES[$key])){
            $params = $_FILES[$key];
        }
        $params = $params? $params: $default;
        return $params;
    }
}
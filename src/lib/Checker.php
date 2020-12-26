<?php

namespace gapi\lib;

class Checker
{

    public static function mobile(string $s): bool
    {
        return self::empty($s) || preg_match("/^\d{7,11}$/", $s);
    }

    public static function empty(string $s): bool
    {
        return empty($s);
    }

    public static function email(string $s): bool
    {
        return preg_match("/^\w+\.?\w*@\w+\.\w*\.?[a-z]{2,}$/", $s);
    }

    public static function currency(string $s): bool
    {
        return preg_match("/^\d+(\.\d+)?$/", $s);
    }

    public static function number(string $s): bool
    {
        return self::isEmpty($s) || preg_match("/^\d+$/", $s);
    }

    public static function numeric(string $s): bool
    {
        return is_numeric($s);
    }

    //邮编
    public static function zip(string $s): bool
    {
        return preg_match("/^\d{6}$/", $s);
    }

    public static function float(string $s): bool
    {
        return preg_match("/^[-\+]?\d+(\.\d+)?$/", $s);
    }

    public static function english(string $s): bool
    {
        return preg_match("/^[A-Za-z]+$/", $s);
    }

    public static function chinese(string $s): bool
    {
        return preg_match("/^[\u4e00-\u9fa5]+$/", $s);
    }

    public static function qq(string $s): bool
    {
        return preg_match("/^1[3456789][0-9]{9}$/", $s);
    }

    public static function wechat(string $s): bool
    {
        return preg_match("/^[a-zA-Z\d_]{6,}$/", $s);
    }

    #日期正则 年月日
    public static function date(string $s): bool
    {
        return preg_match("/^(?:(?!0000)[0-9]{4}([-/.]?)(?:(?:0?[1-9]|1[0-2])\1(?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])\1(?:29|30)|(?:0?[13578]|1[02])\1(?:31))|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)([-/.]?)0?2\2(?:29))$/", $s);
    }

    public static function datetime(string $s): bool
    {
        return preg_match("/^(?:(?!0000)[0-9]{4}([-/.]?)(?:(?:0?[1-9]|1[0-2])\1(?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])\1(?:29|30)|(?:0?[13578]|1[02])\1(?:31))|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)([-/.]?)0?2\2(?:29))\s+([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $s);
    }

    //身份证
    public static function card(string $s): bool
    {
        return preg_match("/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/", $s);
    }


}
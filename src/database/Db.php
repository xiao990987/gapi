<?php

namespace gapi\database;

use gapi\lib\Logger;

class Db extends Database
{
    private static mixed $db = null;
    private static mixed $instance = null;
    private function __construct()
    {
    }

    public function fields($table): array
    {
        $fileds = $this->query("show table columns");

        print_r($fileds);


    }


    /**
     * 数据库驱动必须创建下列方法
     * 并且必须返回正确的值
     * @param $sql
     * @return array|Query
     */
    public function query($sql): array|Query
    {
        try{
            Logger::info("run sql:{$sql}");
            return self::$db->query($sql);
        }catch (\Exception $e){
            Logger::error($this->getError());
        }
        return [];
    }


    public function getError(): string
    {
        return self::$db->getError();
    }

    public function quote($string): string
    {
        return self::$db->quote($string);
    }

    /**
     * @param $sql
     * @return int|bool
     */
    public function exec($sql): int|bool
    {
        try{
            return self::$db->exec($sql);
        }catch (\Exception $e){
            Logger::error($this->getError());
        }
    }

    public static function driver(string $driver):Database
    {
        if (!self::$db instanceof Database) {
            try {
                $driver = "\\gapi\\database\\" . $driver;
                self::$db = new $driver();
            } catch (\ErrorException $e) {
                Logger::error($e->getMessage());
            }
        }
        return self::$db;
    }

    public static function connect($database): Database
    {
        if(!self::$instance instanceof self){
            self::$instance = new self();
            self::driver($database['type'])->_connect($database);
        }
        return self::$instance;
    }

    public function startTrans(): bool
    {
        return self::$db->startTrans();
    }

    public function commit(): bool
    {
        return self::$db->commit();
    }

    public function rollBack(): bool
    {
        return self::$db->rollBack();
    }


}
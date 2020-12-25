<?php

namespace gapi\database;
use gapi\database\MysqliDatabase;
use gapi\database\PdoMysqlDatabase;
use gapi\database\PdoSqlServDatabase;

abstract class Database{
    public string $name;
    public string $driver;

    /**
     * @var string
     */
    public string $table = '';

    /**
     * @var \gapi\database\MysqliDatabase|PdoMysqlDatabase|PdoSqlServDatabase
     */
    public mixed $config = null;

    /**
     * @var string[] $section
     */
    public array $section = [
        'handle' => 'select',
        'select' => '*',
        'insert' => '',
        'set' => '',
        'where' => '',
        'join' => '',
        'group' => '',
        'orderBy' => '',
        'limit' => '',
    ];
    /**
     * @var string $sql
     */
    public string $sql = '';

    public ?string $lastSql = null;

    /**
     * @return string|null
     */
    final public function getLastSql(): string
    {
        return $this->lastSql;
    }
    /**
     * @return string|null
     */
    final public function version(): string|null
    {
        $version = $this->query('SELECT VERSION()');
        return $version ? $version[0]['VERSION()'] : NULL;
    }


    /**
     * 获取最后自增ID
     *
     * @return int
     */
    final public function lastInsertId(): int
    {
        $query = $this->query('SELECT LAST_INSERT_ID()');

        return (int)$query[0]['LAST_INSERT_ID()'];
    }


    final public function fields($table):array
    {

        $fileds = $this->query("show table columns");

        print_r($fileds);


    }







}
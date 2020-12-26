<?php

namespace gapi\database;

abstract class Database
{
    public string $name;
    protected array $config = [];
    /**
     * @var string $sql
     */
    public string $sql = '';

    /**
     * @return string|null
     */
    public function version(): string|null
    {
        $version = $this->query('SELECT VERSION()');
        return $version ? $version[0]['VERSION()'] : NULL;
    }


    /**
     * 获取最后自增ID
     *
     * @return int
     */
    public function lastInsertId(): int
    {
        $query = $this->query('SELECT LAST_INSERT_ID()');
        return (int)$query[0]['LAST_INSERT_ID()'];
    }

    /**
     * 闭包执行事务，返回事务执行的状态
     * @param Closure $callback
     * @return bool
     */
    public function trans(Closure $callback): bool
    {
        try {
            $this->startTrans();
            $callback($this);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollBack();
            return false;
        }
    }

    /**
     * 转义函数
     * 参数可以为多参数或数组，返回数组
     * @param string|array $dat
     * @return string | array
     */
    public function addslashes($data): string|array
    {
        if (is_array($data)) {
            foreach ($data as $k => &$v) {
                $v = $this->addslashes($v);
            }
        } else {
            $data = $this->quote($data);
        }
        return $data;
    }

    /**
     * @param string|array $var
     * @return string|array
     */
    public function stripslashes(array|string $var): array|string
    {
        if (!is_string($var)) {
            foreach ($var as $k => &$v) {
                $this->stripslashes($v);
            }
        } else {
            $var = stripslashes($var);
        }

        return $var;
    }

    /**
     * 数据库驱动必须创建下列方法
     * 并且必须返回正确的值
     * @param $sql
     * @return array|Query
     */
    abstract public function query($sql): array|Query;         //返回值是查询出的数组

    abstract public function getError(): string;            //返回上一个错误信息

    abstract public function quote($string): string; //特殊字符转义

    /**
     * @param $sql
     * @return int|bool
     */
    abstract public function exec($sql): int|bool;           //执行SQL

    abstract public function startTrans(): bool;   //开启事务

    abstract public function commit(): bool;             //关闭事务

    abstract public function rollBack(): bool;           //回滚事务
}
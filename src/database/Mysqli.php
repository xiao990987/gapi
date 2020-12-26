<?php
namespace gapi\database;


use QApi\Config\Database\MysqliDatabase;

/**
 * Class mysqliDriver
 */
class Mysqli extends Database
{
    /**
     * @var \mysqli
     */
    public \mysqli $mysqli;

    /**
     * @param MysqliDatabase $database
     *
     * @return bool
     */
    public function _connect(array $database): void
    {
        //=====使用长连接
        $mysqli = new \mysqli($database['hostname'], $database['username'], $database['password'],$database['database'], $database['hostport']);
        if ($mysqli->connect_error) {
            new \ErrorException('连接数据库失败：' . $mysqli->connect_error);
        } else {
            $this->mysqli = $mysqli;
            $this->mysqli->set_charset($database['charset']);
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function quote($string): string
    {
        $string = mysqli_real_escape_string($this->mysqli, $string);
        if (is_numeric($string)) {
            return $string;
        } else {
            return '\'' . $string . '\'';
        }
    }

    /**
     * 返回错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->mysqli->error;
    }

    /**
     * @param $sql
     * @return array | Query
     */
    public function query($sql): Query|array
    {
        $query = $this->mysqli->query($sql);
        echo $sql;
        $result = [];
        if ($query) {
            while ($row = $query->fetch_assoc()) {
                $data = $row;
                $result[] = $data;
                unset($data);
            }
            unset($query);
            return $result;
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return mysqli_close($this->mysqli);
    }

    /**
     * @param $sql
     * @return bool
     */
    public function exec($sql): bool
    {
        return $this->mysqli->query($sql);
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->mysqli->rollback();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->mysqli->commit();
    }

    /**
     * @return bool
     */
    public function startTrans(): bool
    {
        return $this->mysqli->autocommit(FALSE);
    }
}
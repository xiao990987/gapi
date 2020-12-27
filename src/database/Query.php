<?php

namespace gapi\database;

class Query
{


    public string $sql = '';
    public string $table = '';

    /**
     * @var string[] $section
     */
    public array $section = [
        'select' => '',
        'insert' => '',
        'set' => '',
        'where' => '',
        'orWhere' => '',
        'union' => '',
        'join' => '',
        'group' => '',
        'order' => '',
        'limit' => '',
        'max' => '',
        'min' => '',
        'sum' => '',
        'count' => '',
    ];

    public function __contruct(){

    }

    public function columns(string $table, string $prefix = ''): string
    {
        return "SHOW COLUMNS from {$prefix}{$table};";
    }

    public function tableinfo(string $table, string $prefix = ''): string
    {
        return "show table status like '{$prefix}{$table}'";
    }

    public function getPk()
    {

    }

    public function delete()
    {

    }

    public function group(string $group): self
    {
        $this->section['group'] = " group by {$group}";
        return $this;
    }

    public function isNull($field)
    {
        $this->section['where'][] = " {$field} is null";
        return $this;
    }

    public function isNotNull(string $field): self
    {
        $this->section['where'][] = " {$field} is not null";
        return $this;
    }

    //联合
    public function union(): self
    {
        $this->section['union'] = ' union all';
        return $this;
    }

    public function innerJoin(string $table, string $on1, string $on2): self
    {
        $this->section['join'] = " inner join {$table} where {$on1}={$on2}";
        return $this;
    }

    public function fullJoin(string $table, string $on1, string $on2): self
    {
        $this->section['join'] = " full join {$table} where {$on1}={$on2}";
        return $this;
    }

    public function rightJoin(string $table, string $on1, string $on2): self
    {
        $this->section['join'] = " right join {$table} where {$on1}={$on2}";
        return $this;
    }

    public function leftJoin(string $table, string $on1, string $on2): self
    {
        $this->section['join'] = " left join {$table} where {$on1}={$on2}";
        return $this;
    }

    public function join(string $table, string $on1, string $on2): self
    {
        $this->section['join'] = " join {$table} where {$on1}={$on2}";
        return $this;
    }

    public function insert($insert)
    {

        return $this;
    }

    //解析出完整的SQL命令
    public function compile()
    {


    }


    public function getTable(): string
    {
        return $this->table;
    }


    public function update()
    {

    }

    // - 减
    public function setDec(string $field, int $num = 1)
    {
        $this->section['set'] = "$field={$field}+{$num}";
    }

    // + 加
    public function setInc(string $field, int $num = 1)
    {
        $this->section['set'] = "$field={$field}-{$num}";
    }

    public function table(string $table, string $prefix = ''): self
    {
        $this->table = $table;
        return $this;
    }

    public function from($table, string $prefix = ''): self
    {
        $this->table = $prefix . $table;
        return $this;
    }

    public function where($where)
    {
        $this->section['where'][] = $where;
        return $this;
    }

    public function orWhere($where)
    {
        $this->section['orWhere'][] = $where;
        return $this;
    }

    public function limit(string $limit): self
    {
        $this->section['limit'] = " limit {$limit}";
        return $this;
    }

    public function order(string $order): self
    {
        $this->section['order'] = " order by {$order}";
        return $this;
    }


    public function like(string $field, string $value): self
    {
        $this->section['like'][] = " like {$value}";
        return $this;
    }

    public function in($in): self
    {
        $this->section['in'][] = "in {$in}";
        return $this;
    }

    public function notBetween(string $between): self
    {
        $this->section['between'][] = " not between {$between}";
        return $this;
    }

    public function between(string $between): self
    {
        $this->section['between'][] = " between {$between}";
        return $this;
    }

    public function select(string $field = '*'): self
    {
        $section = $this->section;

        $where = '';
        $flag = 0;
        if ($this->section['where']) {
            $flag = 1;
            $where = implode(' and ', $section['where']);
        }
        if ($this->section['orWhere']) {
            $flag = 1;
            $where = implode(' or ', $section['orWhere']);
        }
        if ($flag) {
            $where = "where {$where}";
        }

        $this->sql = "select {$section['field']} from {$this->table}{$section['union']}{$section['join']}{$where}{$section['group']}{$section['order']}{$section['limit']}";

        return $this;
    }


    public function find()
    {
        $this->section['limit'] = ' limit 1';
        $this->select();
        return $this;
    }


    public function field(string $field): self
    {
        $this->section['field'] = $field;
        return $this;
    }


    public function sum(string $field, string $as = ''): self
    {
        $as = $as == '' ? $field : $as;
        $this->section['field'] = $as == '' ? " sum($field)" : " sum($field) as {$as}";
        return $this;
    }


    public function count(string $field = '*', string $as = ''): self
    {
        $as = $as == '' ? $field : $as;
        $this->section['field'] = $as == '' ? " count($field)" : " count($field) as {$as}";
        return $this;
    }


    public function min(string $field, string $as = ''): self
    {
        $as = $as == '' ? $field : $as;
        $this->section['field'] = $as == '' ? " min($field)" : " min($field) as {$as}";
        return $this;
    }

    public function max(string $field, string $as = ''): self
    {
        $as = $as == '' ? $field : $as;
        $this->section['field'] = $as == '' ? " max($field)" : " max($field) as {$as}";
        return $this;
    }

    public function avg(string $field, string $as = ''): self
    {
        $as = $as == '' ? $field : $as;
        $this->section['field'] = $as == '' ? " avg($field)" : " avg($field) as {$as}";
        return $this;
    }

    public function version(): string
    {
        return 'SELECT VERSION()';
    }

    public function lastInsertId(): string
    {
        return 'SELECT LAST_INSERT_ID()';
    }

    public function __toString()
    {

        $this->compile();

        return $this->sql;
    }


}
<?php

namespace gapi\lib;


class Table
{

    public $table;
    public $fields;
    public $type;
    public $charset;
    public $primary_key;
    public $fulltext_key;
    public $key;
    public $comment;
    public $config;
    public $model;
    public static $auto_increment = false;

    public function __construct($table, $config)
    {
        $this->model = model('Content')->setTableName($table);
        $this->table = $this->model->getTable();
        $this->config = $config;

    }


    public function parseConfig($config)
    {
        if (isset($config['fields']) && !empty($config['fields'])) {
            foreach ($config['fields'] as $name => $field) {
                $this->fields[] = self::parseField($name, $field);
            }
        }
        $this->parseType($config['type'])
            ->parseCharset($config['charset'])
            ->parseComment($config['comment'])
            ->parsePrimaryKey($config)
            ->parseKey($config)
            ->parseUniqueKey($config)
            ->parseFulltextKey($config);

        return $this;
    }


    public function createQuery()
    {
        $this->parseConfig($this->config);
        $params['table'] = $this->model->getTable();
        $params['fields'] = implode(',' . "\n", $this->fields);
        $params['key'] = implode(',' . "\n", $this->key);
        $auto_increment = self::$auto_increment ? ' AUTO_INCREMENT=1 ' : '';
        $params['info'] = $this->type .$auto_increment. $this->charset . $this->comment;
        $params['key'] = $params['key'] == '' ? '' : ',' . $params['key'];
        $tpl = "CREATE TABLE `%TABLE%` (\n%FIELDS%\n%KEY%\n) %INFO% ";

        return str_replace(['%TABLE%', '%FIELDS%', '%KEY%', '%INFO%'], $params, $tpl);
    }


    public function updateQuery()
    {
        $config = $this->config;
        $table_info = $this->model->getTableInfo();
        $fields = $table_info['fields'];
        $fields_type = $table_info['type'];
        $sql = [];
        $config_fields = [];
        if (isset($config['fields']) && !empty($config['fields'])) {
            foreach ($config['fields'] as $name => $field) {
                $config_fields[] = $name;
                if (in_array($name, $fields)) {
                    //在列表中
                    // 检查 类型是否相同
                    if (self::fieldType($field) == $fields_type[$name]) {
                        //相同不需要更新
                    } else {
                        //不相同需要更新
                        $sql[] = $this->modifyField($name, $field);
                    }
                } else {
                    //不在列表中
                    //添加字段
                    $sql[] = $this->addField($name, $field);
                }
            }
            foreach ($fields as $field) {
                //表里面字段 没有在配置里面
                if (!in_array($field, $config_fields)) {
                    $sql[] = $this->delField($field);
                }
            }
        }
        //主键 外键 不允许直接更改  必须在初始化时执行  否则  手动改表 进行优化

        //更新表信息
        $sql[] = $this->setEngine($config['type']);
        $sql[] = $this->setCharset($config['charset']);
        $sql[] = $this->setComment($config['comment']);

        return $sql;
    }

    public static function fieldType($field)
    {
        $type = isset($field['type']) ? $field['type'] : '';
        $unsigned = isset($field['unsigned']) && $field['unsigned'] == true ? ' unsigned' : '';
        return strtolower(trim(preg_replace("/\s+/", ' ', $type . $unsigned)));
    }


    public static function parseField($name, $field)
    {
        $field_item['name'] = $name;
        $field_item['type'] = isset($field['type']) ? $field['type'] : '';
        $field_item['unsigned'] = isset($field['unsigned']) && $field['unsigned'] == true ? ' unsigned' : '';
        $field_item['null'] = isset($field['null']) && $field['null'] == true ? ' NULL' : ' NOT NULL';
        $field_item['auto_increment'] = isset($field['auto_increment']) && $field['auto_increment'] == true ? ' AUTO_INCREMENT' : '';
        if (isset($field['auto_increment']) && $field['auto_increment'] == true) {
            self::$auto_increment = true;
        }
        $field_item['default'] = isset($field['default']) ? " DEFAULT '{$field['default']}'" : '';
        $field_item['comment'] = isset($field['comment']) ? " comment '{$field['comment']}'" : '';

        $tpl = "`%NAME%` %TYPE%%UNSIGNED%%NULL%%DEFULAT%%AUTO_INCREMENT%%COMMENT%";
        return str_replace(['%NAME%', '%TYPE%', '%UNSIGNED%', '%NULL%', '%DEFULAT%', '%AUTO_INCREMENT%', '%COMMENT%'], $field_item, $tpl);
    }


    public function parseType($type)
    {
        $this->type = ' ENGINE=' . $type;
        return $this;
    }

    public function parseCharset($charset)
    {
        $this->charset = ' DEFAULT CHARSET=' . $charset;
        return $this;
    }

    public function parseComment($comment)
    {
        $this->comment = " COMMENT='{$comment}'";
        return $this;
    }

    public function parseUniqueKey($config)
    {
        if (isset($config['unique_key']))
            $this->key[] = $this->parseKeyItem($config['unique_key'], "UNIQUE KEY  [KEY] ");
        return $this;
    }


    public function parsePrimaryKey($config)
    {
        if (isset($config['primary_key']))
            $this->key[] = $this->parseKeyItem($config['primary_key'], "PRIMARY KEY (`[KEY]`)");
        return $this;
    }

    public function parseKey($config)
    {
        if (isset($config['key']))
            $this->key[] = $this->parseKeyItem($config['key'], "KEY [KEY]");
        return $this;
    }

    public function parseFulltextKey($config)
    {
        if (isset($config['fulltext_key']))
            $this->key[] = $this->parseKeyItem($config['fulltext_key'], "FULLTEXT KEY [KEY]");
        return $this;
    }


    public function addField($name, $field)
    {
        return "alter table {$this->table} add " . self::parseField($name, $field);
    }

    public function delField($name)
    {
        return "alter table {$this->table} DROP {$name}";
    }

    public function modifyField($name, $field)
    {
        return "alter table {$this->table} modify " . self::parseField($name, $field);
    }

    public function changeField($name, $new_name, $field)
    {
        return "alter table {$this->table} change {$name} " . self::parseField($name, $field);
    }

    public function setCharset($charset)
    {
        return "alter table {$this->table} default character set {$charset}";
    }

    public function setComment($comment)
    {
        return "alter table {$this->table} comment '{$comment}'";
    }

    public function setEngine($engine)
    {
        return "alter table {$this->table} engine={$engine}";
    }


    private function parseKeyItem($key, $tpl)
    {
        if (isset($key)) {
            if (is_array($key)) {
                $keys = [];
                if (!empty($key)) {
                    foreach ($key as $v) {
                        $keys[] = str_replace(['[KEY]'], [$v], $tpl);
                    }
                }
                $data = implode(',', $keys);
            } else {
                $data = str_replace(['[KEY]'], [$key], $tpl);
            }
        }
        return $data;
    }

}
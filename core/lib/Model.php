<?php
/**
 * 模型类
 * @package     ColorPHP
 * @copyright   Copyright (c) 2016 Jowan
 * @author      Jowan<jowan@17code.net>
 * @link        http://www.17code.net
 * @since       Version 1.0.1
 */
namespace core\lib;

class Model{

    private static $DB;
    public $table;
    public $sql = '';
    public $fields = '*';
    public $where = '';
    public $order_by = '';
    public $limit = '';

    /**
     * 构造方法，连接mysql
     */
    public function __construct()
    {
        if (!self::$DB)
        {
            $host = get_config('db_host');
            $username = get_config('db_user');
            $password = get_config('db_password');
            $dbname = get_config('db_name');
            $dsn = "mysql:host={$host};dbname={$dbname}";

            try
            {
                self::$DB = new \PDO("{$dsn}", $username, $password);
                self::$DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$DB->query('SET NAMES UTF8');
            }
            catch (\PDOException $e)
            {
                sys_error($e->getMessage());
            }
        }
    }

    /**
     * 插入数据
     * @param string $table
     * @param array $data
     * @return int $result
     */
    public function insert($table, $data)
    {
        $this->setTable($table);
        $filed_str = $data_str = '';

        foreach ($data as $k => $v)
        {
            $filed_str .= ",`{$k}`";
            $data_str .= ",'{$v}'";
        }

        $filed_str = trim($filed_str, ',');
        $data_str = trim($data_str, ',');
        $this->sql = "INSERT INTO `{$this->table}` ({$filed_str}) VALUES ({$data_str})";
        $res = $this->doSql('exec');
        $last_id = self::$DB->lastInsertId();

        $result = $last_id ? $last_id : $res;

        return $result;
    }

    /**
     * 获取多条数据
     * @param string $table
     * @return array $result
     */
    public function select($table)
    {
        $this->setTable($table);
        $res = $this->doSql();
        $result = $res->fetchAll(\PDO::FETCH_ASSOC);

        if (strpos($this->fields, 'total') !== FALSE)
        {
            $result = $result[0]['total'];
        }

        $this->fields = '*';
        return $result;
    }

    /**
     * 获取单条数据
     * @param string $table
     * @return array $result
     */
    public function selectOne($table)
    {
        $this->setTable($table);
        $this->limit = 1;
        $res =  $this->doSql();
        $result = $res ? $res->fetch(\PDO::FETCH_ASSOC) : FALSE;

        $this->fields = '*';
        return $result;
    }

    /**
     * 更新数据
     * @param string $table
     * @return int $result
     */
    public function update($table, $data)
    {
        if (!$this->where)
        {
            DEBUG ? sys_error('No where condition in delete action.') : error("500 Err.");
        }

        $data_str = '';

        foreach ($data as $k => $v)
        {
            $data_str .= ",`{$k}` = '{$v}'";
        }

        $data_str = trim($data_str, ',');
        $this->setTable($table);
        $this->sql = "UPDATE `{$this->table}` SET {$data_str}";
        $result = $this->doSql('exec');

        return $result;
    }

    /**
     * 删除数据
     * @param string $table
     * @return int $result
     */
    public function delete($table)
    {
        if (!$this->where)
        {
            DEBUG ? sys_error('No where condition in delete action.') : error("500 Err.");
        }

        $this->setTable($table);
        $this->sql = "DELETE FROM `{$this->table}`";
        $result = $this->doSql('exec');

        return $result;
    }

    /**
     * 统计记录数量
     * @param string $fields
     * @return \core\lib\Model
     */
    public function count($fields = '*')
    {
        $this->fields = "COUNT({$fields}) AS `total`";
        return self::$DB;
    }

    /**
     * 获取字段最大值
     * @param string $fields
     * @return \core\lib\Model
     */
    public function max($fields )
    {
        $this->fields = "MAX({$fields}) AS `total`";
        return self::$DB;
    }

    /**
     * 统计字段最小值
     * @param string $fields
     * @return \core\lib\Model
     */
    public function min($fields)
    {
        $this->fields = "MIN({$fields}) AS `total`";
        return $this;
    }

    /**
     * 统计字段总和
     * @param string $fields
     * @return \core\lib\Model
     */
    public function sum($fields)
    {
        $this->fields = "SUM({$fields}) AS `total`";
        return $this;
    }

    /**
     * 设置数据表
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = get_config('db_prefix') . $table;
    }

    /**
     * 设置查询字段
     * @param string $fields
     * @return \core\lib\Model
     */
    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * 设置查询条件
     * @param mixed $where
     * @return \core\lib\Model
     */
    public function where($where)
    {
        if (is_array($where))
        {
            $i = 0;

            foreach ($where as $k => $v)
            {
                $logic = preg_match('/^[a-zA-Z0-9-_\/]*$/', $k) ? '=' : '';
                $prefix = $i == 0 ? '' : ' AND ';
                $this->where .= "{$prefix}{$k} {$logic} '{$v}' ";
                $i++;
            }
        }
        else
        {
            $this->where = $where;
        }

        $this->where = trim($this->where);
        return $this;
    }

    /**
     * 设置排序方式
     * @param string $order_by
     * @return \core\lib\Model
     */
    public function order_by($order_by)
    {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * 设置结果集数量
     * @param mixed $limit
     * @return \core\lib\Model
     */
    public function limit($limit)
    {
        $this->limit = is_array($limit) ? "{$limit[0]},{$limit[1]}" : $limit;
        return $this;
    }

    /**
     * 获取SQL语句
     */
    public function getSql()
    {
        if ($this->where)
        {
            $this->sql .= " WHERE {$this->where}";
        }

        if ($this->order_by)
        {
            $this->sql .= " ORDER BY {$this->order_by}";
        }

        if ($this->limit !== '') {
            $this->sql .= " LIMIT {$this->limit}";
        }
    }

    /**
     * 执行SQL
     * @param string $mode
     * @return int|\PDOStatement
     */
    public function doSql($mode = 'query')
    {
        if ($mode == 'query')
        {
            $this->sql = "SELECT {$this->fields} FROM `{$this->table}`";
        }

        $this->getSql();

        try
        {
            if ($mode == 'exec')
            {
                $res = self::$DB->exec($this->sql);
                $res = $res !== FALSE ? TRUE : $res;
            }
            else {
                $res = self::$DB->query($this->sql);
            }
        }
        catch (\PDOException $e)
        {
            $str = $e->getMessage();
            $str .= '<div class="line-msg"><span class="tit">SQL:</span> ' . $this->sql .'</div>' ;
            $str .= '<div class="line-msg">' . trim($e->getTraceAsString(), '#') . '</div>';
            $str = str_replace("#", '<br />', $str);

            DEBUG && sys_error($str);
            error("500 Err.");
        }

        // 清空执行条件
        $this->where = '';
        $this->order_by = '';
        $this->limit = '';

        return $res;
    }

    /**
     * 开启自动事务
     */
    public function tranStart()
    {
        self::$DB->setAttribute(\PDO::ATTR_AUTOCOMMIT, 0);
        self::$DB->beginTransaction();
    }

    /**
     * 结束自动事务
     */
    public function transEnd()
    {
        self::$DB->commit();
        self::$DB->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * 重载方法
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        $args = implode(',', $args);
        $result = self::$DB->$name(trim($args, ','));
        return $result;
    }
}
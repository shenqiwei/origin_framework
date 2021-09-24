<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Hash
{
    /**
     * @access private
     * @var object $_Connect 数据库链接对象
     */
    private $Connect;

    /**
     * 构造函数，装在redis数据源连接对象
     * @access public
     * @param object $connect redis主类链接信息
     * @return void
     */
    function __construct($connect)
    {
        $this->Connect = $connect;
    }

    /**
     * 创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int 返回执行结果
     */
    function create($key,$field,$value)
    {
        return $this->Connect->hSet($key, $field, $value);
    }

    /**
     * 获取指定元素内容
     * @access public
     * @param string $key 创建对象元素键
     * @param array $array 字段数组列表
     * @return mixed 返回索引结果
     */
    function createList($key,$array)
    {
        return $this->Connect->hMset($key,$array);
    }

    /**
     * 非替换创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int 返回执行结果
     */
    function createNE($key,$field,$value)
    {
        return $this->Connect->hSetNx($key,$field,$value);
    }

    /**
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return mixed 返回索引结果集
     */
    function get($key,$field)
    {
        if($this->Connect->exists($key)) {
            if ($this->Connect->hExists($key, $field)) {
                $_receipt = $this->Connect->hGet($key, $field);
                if ($_receipt === "nil")
                    $_receipt = null;
            }else{
                $_receipt = null;
            }
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return mixed 返回索引结果
     */
    function lists($key)
    {
        if($this->Connect->exists($key)) {
            $_receipt = $this->Connect->hGetAll($key);
            if ($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param array $fields 字段数组列表
     * @return mixed 返回索引结果
     */
    function getList($key,$fields)
    {
        if($this->Connect->exists($key)) {
            $_receipt = $this->Connect->hMGet($key,$fields);
            if ($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }

    /**
     * 获取hash元素对象区间列表内容(用于redis翻页功能)
     * @access public
     * @param string $key 索引对象元素键
     * @param int $start 起始位置标记
     * @param string $pattern 执行模板(搜索模板)
     * @param int $count 显示总数
     * @return mixed 返回索引结果集
     */
    function limit($key,$start,$pattern,$count)
    {
        $_receipt = $this->Connect->hScan($key,$start,$pattern,$count);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return mixed 返回执行结果
     */
    function values($key)
    {
        return $this->Connect->hVals($key);
    }

    /**
     * 删除元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return int 返回执行结果
     */
    function del($key,$field)
    {
        return $this->Connect->hDel($key,$field);
    }

    /**
     * 设置hash元素对象增量值
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @param int $value 增量值
     * @return mixed 返回执行结果
     */
    function plus($key,$field,$value)
    {
        if (is_float($value)) {
            $_receipt = $this->Connect->hIncrByFloat($key, $field, $value);
        } else {
            $_receipt = $this->Connect->hIncrBy($key, $field, intval($value));
        }
        return $_receipt;
    }

    /**
     * 获取hash元素对象全部字段名(域)
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed 返回执行结果
     */
    function fields($key)
    {
        return $this->Connect->hKeys($key);
    }

    /**
     * 获取hash元素对象字段内容（域）长度
     * @access public
     * @param string $key 索引元素对象键s
     * @return int 返回执行结果
     */
    function len($key)
    {
        return $this->Connect->hLen($key);
    }
}
<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Kernel\Data\Redis;

class Hash
{
    /**
     * @var object $_Connect 数据库链接对象
     */
    private $_Connect = null;
    /**
     * @access public
     * @param string $connect redis主类链接信息
     */
    function __construct($connect)
    {
        $this->_Connect = $connect;
    }
    /**
     * 创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int
     */
    function create($key,$field,$value)
    {
        return $this->_Connect->hSet($key, $field, $value);
    }
    /**
     * @access public
     * @param string $key 创建对象元素键
     * @param array $array 字段数组列表
     * @return mixed
     */
    function createList($key,$array)
    {
        return $this->_Connect->hMset($key,$array);
    }
    /**
     * 非替换创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return int
     */
    function createNE($key,$field,$value)
    {
        return $this->_Connect->hSetNx($key,$field,$value);
    }
    /**
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return mixed
     */
    function get($key,$field)
    {
        if($this->_Connect->exists($key)) {
            if ($this->_Connect->hExists($key, $field)) {
                $_receipt = $this->_Connect->hGet($key, $field);
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
     * @return mixed
     */
    function lists($key)
    {
        if($this->_Connect->exists($key)) {
            $_receipt = $this->_Connect->hGetAll($key);
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
     * @param array $array 字段数组列表
     * @return mixed
     */
    function getList($key,$array)
    {
        if($this->_Connect->exists($key)) {
            $_receipt = $this->_Connect->hMGet($key,$array);
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
     * @return mixed
     */
    function limit($key,$start,$pattern,$count)
    {
        $_receipt = $this->_Connect->hScan($key,$start,$pattern,$count);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return mixed
     */
    function values($key)
    {
        return $this->_Connect->hVals($key);
    }
    /**
     * 删除元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return int
     */
    function del($key,$field)
    {
        return $this->_Connect->hDel($key,$field);
    }
    /**
     * 设置hash元素对象增量值
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @param int $value 增量值
     * @return mixed
     */
    function plus($key,$field,$value)
    {
        if (is_float($value)) {
            $_receipt = $this->_Connect->hIncrByFloat($key, $field, $value);
        } else {
            $_receipt = $this->_Connect->hIncrBy($key, $field, intval($value));
        }
        return $_receipt;
    }
    /**
     * 获取hash元素对象全部字段名(域)
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed
     */
    function fields($key)
    {
        return $this->_Connect->hKeys($key);
    }
    /**
     * 获取hash元素对象字段内容（域）长度
     * @access public
     * @param string $key 索引元素对象键s
     * @return int
     */
    function len($key)
    {
        return $this->_Connect->hLen($key);
    }
}
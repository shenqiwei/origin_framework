<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Kernel\Data\Redis;

class Str
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
     * 创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return mixed
     */
    function create($key,$value)
    {
        $_receipt = $this->_Connect->set($key,$value);
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }
    /**
     * 创建元素对象，并设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $second 生命周期时间（second）
     * @return boolean
     */
    function createSec($key,$value,$second=0)
    {
        $_receipt = $this->_Connect->setex($key,$value,intval($second));
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }
    /**
     * 非覆盖创建元素对象值
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int
     */
    function createOnly($key,$value)
    {
        return $this->_Connect->setnx($key,$value);
    }
    /**
     * 创建元素对象并，设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $milli 生命周期时间（milli）
     * @return boolean
     */
    function createMil($key,$value,$milli=0)
    {
        $_receipt = $this->_Connect->psetex($key,$value,intval($milli));
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key
     * @return mixed
     */
    function get($key)
    {
        $_receipt = $this->_Connect->get($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 叠加（创建）对象元素值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int
     */
    function append($key,$value)
    {
        return $this->_Connect->append($key,$value);
    }
    /**
     * 设置元素对象偏移值
     * @access public
     * @param string $key 被创建对象键名
     * @param int $offset 被创建元素对象内容值
     * @param int $value 偏移系数
     * @return int
     */
    function cBit($key,$offset,$value)
    {
        return $this->_Connect->setBit($key,$offset,$value);
    }
    /**
     * 获取元素对象偏移值
     * @access public
     * @param string $key 被检索对象键名
     * @param int $offset 被创建元素对象内容值
     * @return int
     */
    function gBit($key,$offset)
    {
        return $this->_Connect->getBit($key,$offset);
    }
    /**
     * 检索元素对象值内容长度
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     */
    function getLen($key)
    {
        return $this->_Connect->strlen($key);
    }
    /**
     * 检索元素对象值（区间截取）内容，（大于0的整数从左开始执行，小于0的整数从右开始执行）
     * @access public
     * @param string $key 被检索对象键名
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
     */
    function getRange($key,$start=1,$end=-1)
    {
        $_receipt = $this->_Connect->getRange($key,$start,$end);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 替换原有值内容，并返回原有值内容
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return mixed
     */
    function getRollback($key,$value)
    {
        $_receipt = $this->_Connect->getSet($key,$value);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return boolean
     */
    function createList($columns)
    {
        $_receipt = $this->_Connect->mset($columns);
        if(strtolower($_receipt) === "ok")
            $_receipt = true;
        else
            $_receipt = false;
        return $_receipt;
    }
    /**
     * 非替换创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return int
     */
    function createListOnly($columns)
    {
        return $this->_Connect->msetnx($columns);
    }
    /**
     * 检索元素列表
     * @access public
     * @param array $keys 对应元素列表数组
     * @return mixed
     */
    function getList($keys)
    {
        $_receipt = $this->_Connect->mget($keys);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 对应元素（数据）指定值自增
     * @access public
     * @param string $key 被检索对象键名
     * @param int $increment 自增系数值
     * @return object
     */
    function plus($key,$increment=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($increment) > 1){
            if(is_int($increment))
                # 执行自定义递增操作
                $_receipt = $this->_Connect->incrBy($key,intval($increment));
            else
                # 执行自定义递增(float,double)操作
                $_receipt = $this->_Connect->incrByFloat($key,floatval($increment));
        }else
            # 执行递增1操作
            $_receipt = $this->_Connect->incr($key);
        return $_receipt;
    }
    /**
     * 对应元素（数据）指定值自减
     * @access public
     * @param string $key 被检索对象键名
     * @param int $decrement 自减系数值
     * @return object
     */
    function minus($key,$decrement=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($decrement) > 1)
            # 执行自定义递减操作
            $_receipt = $this->_Connect->decrBy($key,intval($decrement));
        else
            # 执行递减1操作
            $_receipt = $this->_Connect->decr($key);
        return $_receipt;
    }
}
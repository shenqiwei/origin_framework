<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Lists
{
    /**
     * @access private
     * @var object $Connect 数据库链接对象
     */
    private $Connect = null;
    /**
     * @access public
     * @param object $connect redis主类链接信息
     */
    function __construct($connect)
    {
        $this->Connect = $connect;
    }
    /**
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return mixed
     * @context 移出并获取列表的第一个元素
     */
    function removeFirst($keys,$time)
    {
        $_receipt = $this->Connect->blPop($keys,$time);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return mixed
     * @context 获取列表的最后一个元素
     */
    function removeLast($keys,$time)
    {
        $_receipt = $this->Connect->brPop($keys,$time);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param string $write 转存目标对象键
     * @param int $time 最大等待时长
     * @return mixed
     * @context 抽取元素对象值内容，转存至目标元素对象中
     */
    function reIn($key,$write,$time)
    {
        $_receipt = $this->Connect->brpoplpush($key,$write,$time);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引位置参数
     * @return mixed
     * @context 索引元素对象，并返回内容信息（大于0从左开始，小于0从右侧开始）
     */
    function index($key,$index)
    {
        $_receipt = $this->Connect->lIndex($key,$index);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 目标元素值
     * @param mixed $write 写入值
     * @param string $be 插入位置
     * @return int
     * @context 在列表的元素前或者后插入元素
     */
    function insert($key,$value,$write,$be="after")
    {
        if($be === "before"){
            $_location = 0;
        }else{
            $_location = 1;
        }
        return $this->Connect->lInsert($key,$_location,$value,$write);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return int
     * @context 返回列表的长度
     */
    function count($key)
    {
        return $this->Connect->lLen($key);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed
     * @context 移除并返回列表的第一个元素
     */
    function popFirst($key)
    {
        $_receipt = $this->Connect->lPop($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed
     * @context 移除并返回列表的最后一个元素
     */
    function popLast($key)
    {
        $_receipt = $this->Connect->rPop($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key
     * @param string $write
     * @return mixed
     * @context 将元素对象列表的最后一个元素移除并返回，并将该元素添加到另一个列表
     */
    function popWrite($key,$write)
    {
        $_receipt = $this->Connect->rpoplpush($key,$write);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param  mixed $value 插入对象值
     * @return int
     * @context 在列表头部插入一个或多个值
     */
    function inFirst($key,$value)
    {
        return $this->Connect->lPush($key,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return int
     * @context 在列表尾部插入一个或多个值
     */
    function inLast($key,$value)
    {
        return $this->Connect->rPush($key,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return int
     * @context 在已存在的列表头部插入一个值
     */
    function inFFirst($key,$value)
    {
        return $this->Connect->lPushx($key,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return int
     * @context 在已存在的列表尾部插入一个值
     */
    function inFLast($key,$value)
    {
        return $this->Connect->rPushx($key,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return int
     * @context 返回列表中指定区间内的元素
     */
    function range($key,$start,$end)
    {
        return $this->Connect->lRange($key,$start,$end);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 执行(总数)系数 (count > 0: 从表头开始向表尾搜索,count < 0:从表尾开始向表头搜索，count = 0: 删除所有与value相同的)
     * @param mixed $value 操作值
     * @return int
     * @context 根据参数 COUNT 的值，移除列表中与参数 VALUE 相等的元素
     */
    function rem($key,$count,$value)
    {
        return $this->Connect->lRem($key,$count,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引系数
     * @param mixed $value 设置值
     * @return mixed
     * @context 设置索引元素对象
     */
    function indexSet($key,$index,$value)
    {
        return $this->Connect->lSet($key,$index,$value);
    }
    /**
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置系数
     * @param int $end 结束位置系数
     * @return mixed
     * @context 保留指定区间内的元素
     */
    function trim($key,$start,$end)
    {
        return $this->Connect->lTrim($key,$start,$end);
    }
}
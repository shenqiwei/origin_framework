<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Str
{
    /**
     * @access private
     * @var object $Connect 数据库链接对象
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
     * 创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return bool 返回执行结果状态值
     */
    function create($key,$value)
    {
        $receipt = $this->Connect->set($key,$value);
        if(strtolower($receipt) === "ok")
            return true;
        else
            return false;
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
        $receipt = $this->Connect->setex($key,$value,intval($second));
        if(strtolower($receipt) === "ok")
            return true;
        else
            return false;
    }

    /**
     * 非覆盖创建元素对象值
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int 返回影响数据数量
     */
    function createOnly($key,$value)
    {
        return $this->Connect->setnx($key,$value);
    }

    /**
     * 创建元素对象，并设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $milli 生命周期时间（milli）
     * @return boolean 返回执行结果状态值
     */
    function createMil($key,$value,$milli=0)
    {
        $receipt = $this->Connect->psetex($key,$value,intval($milli));
        if(strtolower($receipt) === "ok")
            return true;
        else
            return false;
    }

    /**
     * 获取内容
     * @access public
     * @param string $key
     * @return mixed 返回存入值
     */
    function get($key)
    {
        $receipt = $this->Connect->get($key);
        if ($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 叠加（创建）对象元素值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return int 返回影响数据数量
     */
    function append($key,$value)
    {
        return $this->Connect->append($key,$value);
    }

    /**
     * 设置元素对象偏移值
     * @access public
     * @param string $key 被创建对象键名
     * @param int $value 被创建元素对象内容值
     * @param int $offset 偏移系数
     * @return int 返回执行结果
     */
    function cBit($key,$value,$offset)
    {
        return $this->Connect->setBit($key,$value,$offset);
    }

    /**
     * 获取元素对象偏移值
     * @access public
     * @param string $key 被检索对象键名
     * @param int $value 被创建元素对象内容值
     * @return int 返回执行结果
     */
    function gBit($key,$value)
    {
        return $this->Connect->getBit($key,$value);
    }

    /**
     * 检索元素对象值内容长度
     * @access public
     * @param string $key 被检索对象键名
     * @return int 返回长度信息
     */
    function getLen($key)
    {
        return $this->Connect->strlen($key);
    }

    /**
     * 检索元素对象值（区间截取）内容，（大于0的整数从左开始执行，小于0的整数从右开始执行）
     * @access public
     * @param string $key 被检索对象键名
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return mixed 返回检索数据
     */
    function getRange($key,$start=1,$end=-1)
    {
        $receipt = $this->Connect->getRange($key,$start,$end);
        if($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 替换原有值内容，并返回原有值内容
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return mixed 返回执行对象值
     */
    function getRollback($key,$value)
    {
        $receipt = $this->Connect->getSet($key,$value);
        if($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return boolean 返回执行结果状态值
     */
    function createList($columns)
    {
        $receipt = $this->Connect->mset($columns);
        if(strtolower($receipt) === "ok")
            return true;
        else
            return false;
    }

    /**
     * 非替换创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return int 返回执行结果状态值
     */
    function createListOnly($columns)
    {
        return $this->Connect->msetnx($columns);
    }

    /**
     * 检索元素列表
     * @access public
     * @param array $keys 对应元素列表数组
     * @return mixed 返回检索结果
     */
    function getList($keys)
    {
        $receipt = $this->Connect->mget($keys);
        if($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 对应元素（数据）指定值自增
     * @access public
     * @param string $key 被检索对象键名
     * @param int $increment 自增系数值
     * @return mixed 返回执行结果
     */
    function plus($key,$increment=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($increment) > 1){
            if(is_int($increment))
                # 执行自定义递增操作
                return $this->Connect->incrBy($key,intval($increment));
            else
                # 执行自定义递增(float,double)操作
                return $this->Connect->incrByFloat($key,floatval($increment));
        }else
            # 执行递增1操作
            return $this->Connect->incr($key);
    }

    /**
     * 对应元素（数据）指定值自减
     * @access public
     * @param string $key 被检索对象键名
     * @param int $decrement 自减系数值
     * @return mixed 返回执行结果
     */
    function minus($key,$decrement=1)
    {
        # 判断系数条件是否为大于的参数值
        if(intval($decrement) > 1)
            # 执行自定义递减操作
            return $this->Connect->decrBy($key,intval($decrement));
        else
            # 执行递减1操作
            return $this->Connect->decr($key);
    }
}
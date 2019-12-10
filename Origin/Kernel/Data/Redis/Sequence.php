<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 16:00
 */

namespace Origin\Kernel\Data\Redis;


class Sequence
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
     * 序列增加元素对象内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $param 标记
     * @param mixed $value 存入值
     * @return int
     */
    function add($key,$param,$value)
    {
        return $this->_Connect->zAdd($key,$param,$value);
    }
    /**
     * 返回序列中元素对象内容数
     * @access public
     * @param string $key 索引元素对象键
     * @return int
     */
    function count($key)
    {
        return $this->_Connect->zCard($key);
    }
    /**
     * 序列元素对象中区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间数
     * @param string $max 最大区间数
     * @return int
     */
    function mMCount($key,$min,$max)
    {
        return $this->_Connect->zCount($key,$min,$max);
    }
    /**
     * 序列中元素对象值增加自增系数
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $increment 自增系数
     * @param mixed $value 值
     * @return string
     */
    function ai($key,$increment,$value)
    {
        $_receipt = $this->_Connect->zIncrBy($key,$increment,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 搜索两个序列指定系数成员内容，并存入新的序列中
     * @access public
     * @param string $new 目标序列键
     * @param string $key 索引元素对象键
     * @param mixed $param 索引系数
     * @param string $second 比对索引对象键
     * @return int
     */
    function different($new,$key,$param,$second)
    {
        return $this->_Connect->zInterStore($new,$key,$param,$second);
    }
    /**
     * 序列中字典区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间系数
     * @param string $max 最大区间系数
     * @return int
     */
    function dictCount($key,$min,$max)
    {
        return $this->_Connect->zLexCount($key,$min,$max);
    }
    /**
     * 序列元素对象指定区间内容对象内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return mixed
     */
    function range($key,$min,$max)
    {
        return $this->_Connect->zRange($key,$min,$max);
    }
    /**
     * 序列元素对象指定字典区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min
     * @param string $max
     * @return mixed
     */
    function dictRange($key,$min,$max)
    {
        return $this->_Connect->zRangeByLex($key,$min,$max);
    }
    /**
     * 序列元素对象指定分数区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return mixed
     */
    function limitRange($key,$min,$max)
    {
        return $this->_Connect->zRangeByScore($key,$min,$max);
    }
    /**
     * 返回有序集合中指定成员的索引
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return mixed
     */
    function index($key,$value)
    {
        $_receipt = $this->_Connect->zRank($key,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 移除有序集合中的一个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return int
     */
    function remove($key,$value)
    {
        return $this->_Connect->zRem($key,$value);
    }
    /**
     * 移除有序集合中给定的字典区间的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return int
     */
    function dictRemove($key,$start,$end)
    {
        return $this->_Connect->zRemRangeByLex($key,$start,$end);
    }
    /**
     * 移除有序集中，指定排名(rank)区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return int
     */
    function dictRank($key,$start,$end)
    {
        return $this->_Connect->zRemRangeByRank($key,$start,$end);
    }
    /**
     * 移除有序集中，指定分数（score）区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return int
     */
    function dictScore($key,$min,$max)
    {
        return $this->_Connect->zRemRangeByScore($key,$min,$max);
    }
    /**
     * 返回有序集中，指定区间内的成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return mixed
     */
    function descRange($key,$start,$end)
    {
        $_receipt = $this->_Connect->zRevRange($key,$start,$end);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 返回有序集中，成员的分数值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return string
     */
    function score($key,$value)
    {
        $_receipt = $this->_Connect->zScore($key,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
}
<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Set
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
     * 向集合添加一个或多个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 存入值
     * @return int 返回执行结果
     */
    function add($key,$value)
    {
        return $this->Connect->sAdd($key,$value);
    }

    /**
     * 获取集合内元素数量
     * @access public
     * @param string $key 索引元素对象键
     * @return int 返回执行结果
     */
    function count($key)
    {
        return $this->Connect->sCard($key);
    }

    /**
     * 获取两集合差值
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 比对元素对象键
     * @return mixed 返回执行结果
     */
    function diff($key,$second)
    {
        $_receipt = $this->Connect->sDiff($key,$second);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 获取两集合之间的差值，并存入新集合中
     * @access public
     * @param string $key 索引元素对象
     * @param string $second 比对元素对象键
     * @param string $new 新集合元素对象
     * @return int 返回执行结果
     */
    function different($key,$second,$new=null)
    {
        return $this->Connect->sDiffStore($new,$key,$second);
    }

    /**
     * 判断集合元素对象值是否存在元素对象中
     * @access public
     * @param string $key 索引元素对象键
     * @param string $value 验证值
     * @return int 返回执行结果
     */
    function member($key,$value)
    {
        return $this->Connect->sIsMember($key,$value);
    }

    /**
     * 返回元素对象集合内容
     * @access public
     * @param string $key 索引元素对象键
     * @return mixed 返回索引存储对象内容
     */
    function reSet($key)
    {
        $_receipt = $this->Connect->sMembers($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 元素对象集合值迁移至其他集合中
     * @param string $key 索引元素对象键
     * @param string $second 迁移集合对象
     * @param mixed $value 迁移值
     * @return int 返回执行结果
     */
    function move($key,$second,$value)
    {
        return $this->Connect->sMove($key,$second,$value);
    }

    /**
     * 移除元素对象随机内容值
     * @access public
     * @param string $key 索引元素对象
     * @return mixed 返回执行结果或索引对象内容
     */
    function pop($key)
    {
        $_receipt = $this->Connect->sPop($key);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 随机从元素对象中抽取指定数量元素内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 随机抽调数量
     * @return mixed 返回执行结果或索引对象内容
     */
    function randMember($key,$count=1)
    {
        if($count > 1)
            $_receipt = $this->Connect->sRandMember($key);
        else
            $_receipt = $this->Connect->sRandMember($key,$count);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 移除元素对象中指定元素内容
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return int 返回执行结果
     */
    function remove($key,$value)
    {
        return $this->Connect->sRem($key,$value);
    }

    /**
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return mixed 返回执行结果或集合内容
     */
    function merge($key,$second)
    {
        $_receipt = $this->Connect->sUnion($key,$second);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }

    /**
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $new 存储指向集合键
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return int 返回执行结果
     */
    function mergeTo($new,$key,$second)
    {
        return $this->Connect->sUnionStore($new,$key,$second);
    }

    /**
     * 迭代元素对象指定结构内容
     * @access public
     * @param string $key 索引元素对象
     * @param string $value 索引值
     * @param int $cursor 执行标尺
     * @param string $pattern 操作参数
     * @return mixed 返回执行结果
     */
    function tree($key,$value,$cursor=0,$pattern="match")
    {
        $_receipt = $this->Connect->sScan($key,$cursor,$pattern,$value);
        if ($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
}
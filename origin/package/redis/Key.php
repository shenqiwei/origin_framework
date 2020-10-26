<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context: Origin框架Redis封装类
 */
namespace Origin\Package\Redis;

class Key
{
    /**
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
     * 删除元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     */
    function del($key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->del($key);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }
    /**
     * 序列化元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return mixed
     */
    function dump($key)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->dump($key);
            if($_receipt === "nil")
                $_receipt = null;
        }else{
            $_receipt = null;
        }
        return $_receipt;
    }
    /**
     * 使用时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     */
    function setTSC($key,$timestamp)
    {
        return $this->Connect->expireAt($key,$timestamp);
    }
    /**
     * 使用秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $second 时间戳
     * @return bool
     */
    function setSec($key,$second)
    {
        return $this->Connect->expire($key,$second);
    }
    /**
     * 使用毫秒时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool
     */
    function setTSM($key,$timestamp)
    {
        return $this->Connect->pExpireAt($key,$timestamp);
    }
    /**
     * 使用毫秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $millisecond 时间戳
     * @return bool
     */
    function setMil($key,$millisecond)
    {
        return $this->Connect->pExpire($key,$millisecond);
    }
    /**
     * 移除元素目标生命周期限制
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     */
    function rmCycle($key)
    {
        return $this->Connect->persist($key);
    }
    /**
     * 获取元素对象剩余周期时间(毫秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     */
    function remaining($key)
    {
        return $this->Connect->pttl($key);
    }
    /**
     * 获取元素对象剩余周期时间(秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     */
    function remain($key)
    {
        return $this->Connect->ttl($key);
    }
    /**
     * 获取搜索相近元素对象键
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return mixed
     */
    function keys($closeKey)
    {
        $_receipt = $this->Connect->keys($closeKey);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 随机返回元素键
     * @access public
     * @return mixed
     */
    function randKey()
    {
        $_receipt = $this->Connect->randomKey();
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 重命名元素对象
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return bool
     */
    function rnKey($key,$newKey)
    {
        if($this->Connect->exists($key)){
            $_receipt = $this->Connect->rename($key, $newKey);
        }else{
            $_receipt = false;
        }
        return $_receipt;
    }
    /**
     * 非重名元素对象重命名
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return int
     */
    function irnKey($key,$newKey)
    {
        return $this->Connect->renameNx($key, $newKey);
    }
    /**
     * 获取元素对象内容数据类型
     * @access public
     * @param string $key 被检索对象键名
     * @return string
     */
    function type($key)
    {
        return $this->Connect->type($key);
    }
    /**
     * 将元素对象存入数据库
     * @access public
     * @param string $key 被检索对象键名
     * @param string $database 对象数据库名
     * @return int
     */
    function inDB($key,$database)
    {
        return $this->Connect->move($key, $database);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 15:47
 */

namespace Origin\Kernel\Data\Redis;


class Key
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
     * 删除元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     */
    function del($key)
    {
        if($this->_Connect->exists($key)){
            $_receipt = $this->_Connect->del($key);
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
        if($this->_Connect->exists($key)){
            $_receipt = $this->_Connect->dump($key);
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
        return $this->_Connect->expireAt($key,$timestamp);
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
        return $this->_Connect->expire($key,$second);
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
        return $this->_Connect->pExpireAt($key,$timestamp);
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
        return $this->_Connect->pExpire($key,$millisecond);
    }
    /**
     * 移除元素目标生命周期限制
     * @access public
     * @param string $key 被检索对象键名
     * @return bool
     */
    function rmCycle($key)
    {
        return $this->_Connect->persist($key);
    }
    /**
     * 获取元素对象剩余周期时间(毫秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return int
     */
    function pTTL($key)
    {
        return $this->_Connect->pttl($key);
    }
    /**
     * 获取元素对象剩余周期时间(秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function TTL($key)
    {
        return $this->_Connect->ttl($key);
    }
    /**
     * 获取搜索相近元素对象键
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return object
     */
    function keys($closeKey)
    {
        $_receipt = $this->_Connect->keys($closeKey);
        if($_receipt === "nil")
            $_receipt = null;
        return $_receipt;
    }
    /**
     * 随机返回元素键
     * @access public
     * @return object
     */
    function randKey()
    {
        $_receipt = $this->_Connect->randomKey();
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
        if($this->_Connect->exists($key)){
            $_receipt = $this->_Connect->rename($key, $newKey);
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
        return $this->_Connect->renameNx($key, $newKey);
    }
    /**
     * 获取元素对象内容数据类型
     * @access public
     * @param string $key 被检索对象键名
     * @return string
     */
    function type($key)
    {
        return $this->_Connect->type($key);
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
        return $this->_Connect->move($key, $database);
    }
}
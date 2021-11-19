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
     * 删除元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return bool 返回执行结果状态值
     */
    function del($key)
    {
        if($this->Connect->exists($key))
            return $this->Connect->del($key);
        else
            return false;
    }

    /**
     * 序列化元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return mixed 返回索引结果
     */
    function dump($key)
    {
        if($this->Connect->exists($key)){
            $receipt = $this->Connect->dump($key);
            if($receipt === "nil")
                $receipt = null;
        }else
            $receipt = null;
        return $receipt;
    }

    /**
     * 使用时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return bool 返回执行结果状态值
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
     * @return bool 返回执行结果状态值
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
     * @return bool 返回执行结果状态值
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
     * @return bool 返回执行结果状态值
     */
    function setMil($key,$millisecond)
    {
        return $this->Connect->pExpire($key,$millisecond);
    }

    /**
     * 移除元素目标生命周期限制
     * @access public
     * @param string $key 被检索对象键名
     * @return bool 返回执行结果状态值
     */
    function rmCycle($key)
    {
        return $this->Connect->persist($key);
    }

    /**
     * 获取元素对象剩余周期时间(毫秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return int 返回索引结果
     */
    function remaining($key)
    {
        return $this->Connect->pttl($key);
    }

    /**
     * 获取元素对象剩余周期时间(秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return int 返回索引结果
     */
    function remain($key)
    {
        return $this->Connect->ttl($key);
    }

    /**
     * 获取搜索相近元素对象键
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return mixed 返回索引结果集
     */
    function keys($closeKey)
    {
        $receipt = $this->Connect->keys($closeKey);
        if($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 随机返回元素键
     * @access public
     * @return mixed 返回执行结果集
     */
    function randKey()
    {
        $receipt = $this->Connect->randomKey();
        if($receipt === "nil")
            $receipt = null;
        return $receipt;
    }

    /**
     * 重命名元素对象
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return bool 返回执行结果状态值
     */
    function rnKey($key,$newKey)
    {
        if($this->Connect->exists($key))
            return $this->Connect->rename($key, $newKey);
        else
            return false;
    }

    /**
     * 非重名元素对象重命名
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return int 返回执行结果
     */
    function irnKey($key,$newKey)
    {
        return $this->Connect->renameNx($key, $newKey);
    }

    /**
     * 获取元素对象内容数据类型
     * @access public
     * @param string $key 被检索对象键名
     * @return string 返回索引结果
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
     * @return int 返回执行结果
     */
    function inDB($key,$database)
    {
        return $this->Connect->move($key, $database);
    }
}
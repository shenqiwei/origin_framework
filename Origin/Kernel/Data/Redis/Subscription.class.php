<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 16:11
 */

namespace Origin\Kernel\Data\Redis;


class Subscription
{
    /**
     * @var object $_Connect 数据库链接对象
     */
    private $_Connect = null;
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    /**
     * @var mixed $_Value
     * 被索引键值内容信息
     */
    protected $_Value = null;
    /**
     * @access public
     * @param string $connect redis主类链接信息
     */
    function __construct($connect)
    {
        $this->_Connect = $connect;
    }
    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     */
    function __setSQL($object)
    {
        $this->_Object = $object;
    }
    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object
     */
    protected function __getSQL()
    {
        return $this->_Object;
    }
    /**
     * 订阅一个或多个符合给定模式的频道
     * @access public
     * @param array $pattern 订阅频道参数数组
     * @param mixed $callback 回调参数对象
     * @return object
     */
    function channel($pattern,$callback)
    {
        try{
            $this->_Value = $this->_Connect->psubscribe($pattern,$callback);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 订阅与发布系统状态
     * @access public
     * @param string $command 子操作命令
     * @param mixed $argument 命令摘要
     * @return object
     */
    function status($command,$argument)
    {
        try{
            $this->_Value = $this->_Connect->pubsub($command,$argument);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 信息发送到指定的频道
     * @access public
     * @param string $channel 对象频道
     * @param string $message 发送信息
     * @return object
     */
    function message($channel,$message)
    {
        try{
            $this->_Value = $this->_Connect->publish($channel,$message);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 退订所有给定模式的频道
     * @access public
     * @param string $channel 退订频道
     * @return object
     */
    function unChannel($channel)
    {
        try{
            $this->_Value = $this->_Connect->punsubscribe($channel);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 订阅给定的一个或多个频道的信息
     * @access public
     * @param array $channel 订阅频道参数数组
     * @param mixed $callback 回调参数对象
     * @return object
     */
    function mChannel($channel,$callback)
    {
        try{
            $this->_Value = $this->_Connect->subscribe($channel,$callback);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 退订给定的一个或多个频道的信息
     * @access public
     * @param array $channel
     * @return object
     */
    function unMChannel($channel)
    {
        try{
            $this->_Value = $this->_Connect->unsubscribe($channel);
            if ($this->_Value === "nil")
                $this->_Value = null;
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取当前操作所获取内容，或执行对象内容
     * @access public
     * @return mixed
     */
    function value()
    {
        return $this->_Value;
    }
}
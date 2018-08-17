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
     * 删除元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function del($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->get($key);
                if($this->_Value === "nil")
                    $this->_Value = null;
                $this->_Connect->delete($key);
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 序列化元素对象内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function dump($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->dump($key);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return object
     */
    function setTSC($key,$timestamp)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->expireAt($key,$timestamp);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $second 时间戳
     * @return object
     */
    function setSec($key,$second)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->expire($key,$second);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用毫秒时间戳设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $timestamp 时间戳
     * @return object
     */
    function setTSM($key,$timestamp)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->pExpireAt($key,$timestamp);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 使用毫秒计时单位设置元素对象生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param int $millisecond 时间戳
     * @return object
     */
    function setMil($key,$millisecond)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->pExpire($key,$millisecond);
                if($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 移除元素目标生命周期限制
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function rmCycle($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->persist($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象剩余周期时间(毫秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function pTTL($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->pttl($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象剩余周期时间(秒)
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function TTL($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->ttl($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取搜索相近元素对象键
     * @access public
     * @param string $closeKey 相近元素对象（key*）
     * @return object
     */
    function keys($closeKey)
    {
        try{
            $this->_Value = $this->_Connect->keys($closeKey);
            if($this->_Value === "nil")
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
     * 随机返回元素键
     * @access public
     * @return object
     */
    function randKey()
    {
        try{
            $this->_Value = $this->_Connect->randomKey();
            if($this->_Value === "nil")
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
     * 重命名元素对象
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return object
     */
    function rnKey($key,$newKey)
    {
        try {
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->rename($key, $newKey);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }else{
                $this->_Value = "ERROR_NOT_HAS_KEY_OBJECT";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 非重名元素对象重命名
     * @access public
     * @param string $key 被检索对象键名
     * @param string $newKey 新命名
     * @return object
     */
    function irnKey($key,$newKey)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->renameNx($key, $newKey);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }else{
                $this->_Value = "ERROR_NOT_HAS_KEY_OBJECT";
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 获取元素对象内容数据类型
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function type($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->type($key);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 将元素对象存入数据库
     * @access public
     * @param string $key 被检索对象键名
     * @param string $database 对象数据库名
     * @return object
     */
    function inDB($key,$database)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->move($key, $database);
                if ($this->_Value === "nil")
                    $this->_Value = null;
            }
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
<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 15:49
 */

namespace Origin\Kernel\Data\Redis;


class RedisSet
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
     * 集合：向集合添加一个或多个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 存入值
     * @return object
     */
    function add($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->sAdd($key,$value);
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
     * 获取集合内元素数量
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function count($key)
    {
        try{
            $this->_Value = $this->_Connect->sCard($key);
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
     * 获取两集合差值
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 比对元素对象键
     * @return object
     */
    function diff($key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sDiff($key,$second);
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
     * 获取两集合之间的差值，并存入新集合中
     * @access public
     * @param string $new 新集合元素对象
     * @param string $key 索引元素对象
     * @param string $second 比对元素对象键
     * @return object
     */
    function different($new=null,$key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                if(!is_null($new)){
                    $this->_Value = $this->_Connect->sDiffStore($new,$key,$second);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }else{
                    $this->_Value = "INVALID_COLUMN_OBJECT";
                }
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
     * 判断集合元素对象值是否存在元素对象中
     * @access public
     * @param string $key 索引元素对象键
     * @param string $value 验证值
     * @return object
     */
    function member($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sIsMember($key,$value);
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
     * 返回元素对象集合内容
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function return($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sMembers($key);
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
     * 元素对象集合值迁移至其他集合中
     * @param string $key 索引元素对象键
     * @param string $second 迁移集合对象
     * @param mixed $value 迁移值
     * @return object
     */
    function move($key,$second,$value)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                if(!is_null($value)){
                    $this->_Value = $this->_Connect->sMove($key,$second,$value);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
                }else{
                    $this->_Value = "INVALID_COLUMN_OBJECT";
                }
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
     * 移除元素对象随机内容值
     * @access public
     * @param string $key 索引元素对象
     * @return object
     */
    function pop($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sPop($key);
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
     * 随机从元素对象中抽取指定数量元素内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 随机抽调数量
     * @return object
     */
    function randMember($key,$count=1)
    {
        try{
            if($this->_Connect->exists($key)){
                if($count > 1)
                    $this->_Value = $this->_Connect->sRandMember($key);
                else
                    $this->_Value = $this->_Connect->sRandMember($key,$count);
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
     * 移除元素对象中指定元素内容
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return object
     */
    function remove($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sRem($key,$value);
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
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return object
     */
    function merge($key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sUnion($key,$second);
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
     * 返回指定两个集合对象的并集
     * @access public
     * @param string $new 存储指向集合键
     * @param string $key 索引元素对象键
     * @param string $second 索引元素对象键
     * @return object
     */
    function mergeTo($new,$key,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->sUnionStore($new,$key,$second);
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
     * 迭代元素对象指定结构内容
     * @access public
     * @param string $key 索引元素对象
     * @param int $cursor 执行标尺
     * @param string $pattern 操作参数
     * @param string $value 索引值
     * @return object
     */
    function tree($key,$cursor=0,$pattern="match",$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->sScan($key,$cursor,$pattern,$value);
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
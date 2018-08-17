<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 16:00
 */

namespace Origin\Kernel\Data\Redis;


class RedisSeq
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
     * 序列增加元素对象内容值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $param 标记
     * @param mixed $value 存入值
     * @return object
     */
    function add($key,$param,$value)
    {
        try{
            $this->_Value = $this->_Connect->zAdd($key,$param,$value);
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
     * 返回序列中元素对象内容数
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function count($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zCard($key);
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
     * 序列元素对象中区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间数
     * @param string $max 最大区间数
     * @return object
     */
    function mMCount($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zCount($key,$min,$max);
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
     * 序列中元素对象值增加自增系数
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $increment 自增系数
     * @param mixed $value 值
     * @return object
     */
    function ai($key,$increment,$value)
    {
        try{
            $this->_Value = $this->_Connect->zIncrBy($key,$increment,$value);
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
     * 搜索两个序列指定系数成员内容，并存入新的序列中
     * @access public
     * @param string $new 目标序列键
     * @param string $key 索引元素对象键
     * @param mixed $param 索引系数
     * @param string $second 比对索引对象键
     * @return object
     */
    function different($new,$key,$param,$second)
    {
        try{
            if($this->_Connect->exists($key) and $this->_Connect->exists($second)){
                $this->_Value = $this->_Connect->zInterStore($new,$key,$param,$second);
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
     * 序列中字典区间值数量
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min 最小区间系数
     * @param string $max 最大区间系数
     * @return object
     */
    function dictCount($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zLexCount($key,$min,$max);
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
     * 序列元素对象指定区间内容对象内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
     */
    function range($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRange($key,$min,$max);
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
     * 序列元素对象指定字典区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param string $min
     * @param string $max
     * @return object
     */
    function dictRange($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRangeByLex($key,$min,$max);
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
     * 序列元素对象指定分数区间内容
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
     */
    function limitRange($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRangeByScore($key,$min,$max);
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
     * 返回有序集合中指定成员的索引
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return object
     */
    function index($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRank($key,$value);
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
     * 移除有序集合中的一个成员
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 移除值
     * @return object
     */
    function remove($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRem($key,$value);
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
     * 移除有序集合中给定的字典区间的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
     */
    function dictRemove($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByLex($key,$start,$end);
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
     * 移除有序集中，指定排名(rank)区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
     */
    function dictRank($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByRank($key,$start,$end);
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
     * 移除有序集中，指定分数（score）区间内的所有成员
     * @access public
     * @param string $key 索引元素对象键
     * @param int $min
     * @param int $max
     * @return object
     */
    function dictScore($key,$min,$max)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRemRangeByRank($key,$min,$max);
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
     * 返回有序集中，指定区间内的成员
     * @access public
     * @param string $key 索引元素对象键
     * @param string $start
     * @param string $end
     * @return object
     */
    function descRange($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zRevRange($key,$start,$end);
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
     * 返回有序集中，成员的分数值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 索引值
     * @return object
     */
    function score($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->zScore($key,$value);
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
     * zUnionStore 计算给定的一个或多个有序集的并集，并存储在新的 key 中
     * zScan 迭代有序集合中的元素（包括元素成员和元素分值）
     * 在结构实际应用中转化后函数结构过于繁琐，所以在实例应用中，直接使用该函数直接应用
     */
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
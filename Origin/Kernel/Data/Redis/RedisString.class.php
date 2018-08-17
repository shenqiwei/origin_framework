<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 15:47
 */

namespace Origin\Kernel\Data\Redis;


class RedisString
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
     * 创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function create($key,$value){
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->set($key,$value);
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
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
     * @access public
     * @param string $columns 被创建元素对象列表
     *
     */
    /**
     * 创建元素对象，并设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $second 生命周期时间（second）
     * @return object
     */
    function createSec($key,$value,$second=0)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->setex($key,$value,intval($second));
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}, cycle:{$second} seconds";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
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
     * 非覆盖创建元素对象值
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function createNE($key,$value)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->setnx($key,$value);
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}";
            }else{
                # 回写冲突对象内容
                $this->_Value = "ERROR_KEY_IS_ALREADY_EXISTS";
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
     * 创建元素对象并，设置生命周期
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @param int $milli 生命周期时间（milli）
     * @return object
     */
    function createMil($key,$value,$milli=0)
    {
        try{
            # 判断当前对象元素是否已被创建
            if(!$this->_Connect->exists($key)){
                # 创建对象元素内容并赋值
                $this->_Connect->psetex($key,$value,intval($milli));
                # 回写写入对象元素内容
                $this->_Value = "{$key}:{$value}, cycle:{$milli} milliseconds";
            }else{
                # 回写冲突对象内容
                $this->_Value = "{$key}:".$this->_Connect->get($key);
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
     * 覆盖原创建元素对象值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function reCreate($key,$value){
        try{
            # 创建对象元素内容并赋值
            $this->_Connect->set($key,$value);
            # 回写写入对象元素内容
            $this->_Value = "{$key}:{$value}";
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 设置元素对象偏移值
     * @access public
     * @param string $key 被创建对象键名
     * @param int $offset 被创建元素对象内容值
     * @param int $value 偏移系数
     * @return object
     */
    function cBit($key,$offset,$value)
    {
        try{
            if($this->_Connect->exists($key)){

                $this->_Value = $this->_Connect->setBit($key,$offset,$value);
                if($this->_Value === "nil")
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
     * 叠加（创建）对象元素值内容
     * @access public
     * @param string $key 被创建对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function append($key,$value)
    {
        try{
            # 叠加（创建）对象元素内容并赋值
            $this->_Connect->append($key,$value);
            # 回写写入对象元素内容
            $this->_Value = "{$key}:".$this->_Connect->get($key);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            print('Error:'.$e->getMessage());
            exit();
        }
        return $this->_Object;
    }
    /**
     * 检索元素对象值内容
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function get($key){
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->get($key);
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
     * 获取元素对象偏移值
     * @access public
     * @param string $key 被检索对象键名
     * @param int $offset 被创建元素对象内容值
     * @return object
     */
    function gBit($key,$offset)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getBit($key,$offset);
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
     * 检索元素对象值内容长度
     * @access public
     * @param string $key 被检索对象键名
     * @return object
     */
    function getLen($key)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->strlen($key);
            }else{
                $this->_Value = 0;
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
     * 检索元素对象值（区间截取）内容，（大于0的整数从左开始执行，小于0的整数从右开始执行）
     * @access public
     * @param string $key 被检索对象键名
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
     */
    function getRange($key,$start=1,$end=-1)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getRange($key,$start,$end);
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
     * 检索元素对象进行初始化内容
     * @access public
     * @param string $key 被检索对象键名
     * @param mixed $value 被创建元素对象内容值
     * @return object
     */
    function getRollback($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->getSet($key,$value);
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
     * 创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return object
     */
    function createList($columns)
    {
        try{
            if(is_array($columns)){
                $this->_Connect->mset($columns);
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
     * 非替换创建元素列表
     * @access public
     * @param array $columns 对应元素列表数组
     * @return object
     */
    function createLNE($columns)
    {
        try{
            if(is_array($columns)){
                $this->_Connect->msetnx($columns);
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
     * 检索元素列表
     * @access public
     * @param array $keys 对应元素列表数组
     * @return object
     */
    function getList($keys)
    {
        try{
            if(is_array($keys)){
                $this->_Value = $this->_Connect->mget($keys);
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
     * 对应元素（数据）指定值自增
     * @access public
     * @param string $key 被检索对象键名
     * @param int $increment 自增系数值
     * @return object
     */
    function plus($key,$increment=1)
    {
        try{
            # 判断执行元素对象是否为数字
            if($this->_Connect->exists($key) and is_numeric($this->_Connect->get($key))){
                # 判断系数条件是否为大于的参数值
                if(intval($increment) > 1){
                    if(is_int($increment)){
                        # 执行自定义递增操作
                        $this->_Connect->incrBy($key,intval($increment));
                    }else{
                        # 执行自定义递增(float,double)操作
                        $this->_Connect->incrByFloat($key,floatval($increment));
                    }
                }else{
                    # 执行递增1操作
                    $this->_Connect->incr($key);
                }
                $this->_Value = $this->_Connect->get($key);
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
     * 对应元素（数据）指定值自减
     * @access public
     * @param string $key 被检索对象键名
     * @param int $decrement 自减系数值
     * @return object
     */
    function minus($key,$decrement=1)
    {
        try{
            # 判断执行元素对象是否为数字
            if($this->_Connect->exists($key) and is_numeric($this->_Connect->get($key))){
                # 判断系数条件是否为大于的参数值
                if(intval($decrement) > 1){
                    # 执行自定义递减操作
                    $this->_Connect->decrBy($key,intval($decrement));
                }else{
                    # 执行递减1操作
                    $this->_Connect->decr($key);
                }
                $this->_Value = $this->_Connect->get($key);
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
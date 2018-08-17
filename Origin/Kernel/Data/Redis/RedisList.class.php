<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 15:48
 */

namespace Origin\Kernel\Data\Redis;


class RedisList
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
     * 移出并获取列表的第一个元素
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return object
     */
    function removeFirst($keys,$time)
    {
        try{
            if(is_array($keys)) {
                $this->_Value = $this->_Connect->blPop($keys,$time);
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
     * 获取列表的最后一个元素
     * @access public
     * @param array $keys 索引元素对象列表
     * @param int $time 最大等待时长
     * @return object
     */
    function removeLast($keys,$time)
    {
        try{
            if(is_array($keys)) {
                $this->_Value = $this->_Connect->brPop($keys,$time);
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
     * 抽取元素对象值内容，转存至目标元素对象中
     * @access public
     * @param string $key 索引元素对象键
     * @param string $write 转存目标对象键
     * @param int $time 最大等待时长
     * @return object
     */
    function reIn($key,$write,$time)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->brpoplpush($key,$write,$time);
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
     * 索引元素对象，并返回内容信息（大于0从左开始，小于0从右侧开始）
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引位置参数
     * @return object
     */
    function index($key,$index){
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->lIndex($key,$index);
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
     * 在列表的元素前或者后插入元素
     * @access public
     * @param string $key 索引元素对象键
     * @param string $be 插入位置
     * @param mixed $value 目标元素值
     * @param mixed $write 写入值
     * @return object
     */
    function insert($key,$be="after",$value,$write)
    {
        try{
            if($this->_Connect->exists($key)) {
                if($be === "before"){
                    $_location = 0;
                }else{
                    $_location = 1;
                }
                $this->_Value = $this->_Connect->lInsert($key,$_location,$value,$write);
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
     * 返回列表的长度
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function count($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = intval($this->_Connect->lLen($key));
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
     * 移除并返回列表的第一个元素
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function popFirst($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->lPop($key);
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
     * 移除并返回列表的最后一个元素
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function popLast($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->rPop($key);
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
     * 将元素对象列表的最后一个元素移除并返回，并将该元素添加到另一个列表
     * @access public
     * @param string $key
     * @param string $write
     * @return object
     */
    function popWrite($key,$write)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->rpoplpush($key,$write);
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
     * 在列表头部插入一个或多个值
     * @access public
     * @param string $key 索引元素对象键
     * @param  mixed $value 插入对象值
     * @return object
     */
    function inFirst($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->lPush($key,$value);
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
     * 在列表尾部插入一个或多个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
     */
    function inLast($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->rPush($key,$value);
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
     * 在已存在的列表头部插入一个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
     */
    function inFFirst($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lPushx($key,$value);
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
     * 在已存在的列表尾部插入一个值
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 插入对象值
     * @return object
     */
    function inFLast($key,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->rPushx($key,$value);
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
     * 返回列表中指定区间内的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置参数
     * @param int $end 结束位置参数
     * @return object
     */
    function range($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lRange($key,$start,$end);
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
     * 根据参数 COUNT 的值，移除列表中与参数 VALUE 相等的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $count 执行(总数)系数 (count > 0: 从表头开始向表尾搜索,count < 0:从表尾开始向表头搜索，count = 0: 删除所有与value相同的)
     * @param mixed $value 操作值
     * @return object
     */
    function rem($key,$count,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lRem($key,$count,$value);
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
     * 设置索引元素对象
     * @access public
     * @param string $key 索引元素对象键
     * @param int $index 索引系数
     * @param mixed $value 设置值
     * @return object
     */
    function indexSet($key,$index,$value)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lSet($key,$index,$value);
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
     * 保留指定区间内的元素
     * @access public
     * @param string $key 索引元素对象键
     * @param int $start 起始位置系数
     * @param int $end 结束位置系数
     * @return object
     */
    function trim($key,$start,$end)
    {
        try{
            if($this->_Connect->exists($key)){
                $this->_Value = $this->_Connect->lTrim($key,$start,$end);
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
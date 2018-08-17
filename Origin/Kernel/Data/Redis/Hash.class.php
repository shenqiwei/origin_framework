<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 15:56
 */

namespace Origin\Kernel\Data\Redis;


class Hash
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
     * 创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
     */
    function create($key,$field,$value)
    {
        try{
            if (!$this->_Connect->hExists($key, $field)) {
                $this->_Value = $this->_Connect->hSet($key, $field, $value);
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
     * 覆盖创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
     */
    function reCreate($key,$field,$value)
    {
        try{
            $this->_Value = $this->_Connect->hSet($key, $field, $value);
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
     * @access public
     * @param string $key 创建对象元素键
     * @param array $array 字段数组列表
     * @return object
     */
    function createList($key,$array)
    {
        try {
            if (is_array($array)) {
                $this->_Value = $this->_Connect->hMset($key,$array);
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
     * 非替换创建hash元素对象内容
     * @access public
     * @param string $key 创建对象元素键
     * @param string $field hash对象字段名(域)
     * @param mixed $value 内容值
     * @return object
     */
    function createNE($key,$field,$value)
    {
        try{
            $this->_Value = $this->_Connect->hSetNx($key,$field,$value);
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
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return object
     */
    function hashGet($key,$field)
    {
        try{
            if($this->_Connect->exists($key)) {
                if ($this->_Connect->hExists($key, $field)) {
                    $this->_Value = $this->_Connect->hGet($key, $field);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
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
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return object
     */
    function list($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hGetAll($key);
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
     * 获取hash元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param array $array 字段数组列表
     * @return object
     */
    function getList($key,$array)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hMGet($key,$array);
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
     * 获取hash元素对象区间列表内容(用于redis翻页功能)
     * @access public
     * @param string $key 索引对象元素键
     * @param int $start 起始位置标记
     * @param string $pattern 执行模板(搜索模板)
     * @param int $count 显示总数
     * @return object
     */
    function limit($key,$start,$pattern,$count)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hScan($key,$start,$pattern,$count);
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
     * 返回hash元素对象列表
     * @access public
     * @param string $key 索引对象元素键
     * @return object
     */
    function values($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hVals($key);
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
     * 删除元素对象内容
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @return object
     */
    function del($key,$field)
    {
        try{
            if($this->_Connect->exists($key)){
                if($this->_Connect->hExists($key,$field)) {
                    $this->_Value = $this->_Connect->hDel($key,$field);
                    if ($this->_Value === "nil")
                        $this->_Value = null;
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
     * 设置hash元素对象增量值
     * @access public
     * @param string $key 索引对象元素键
     * @param string $field hash对象字段名(域)
     * @param int $value 增量值
     * @return object
     */
    function plus($key,$field,$value)
    {
        try{
            if($this->_Connect->exists($key)) {
                if ($this->_Connect->hExists($key, $field)) {
                    if (is_float($value)) {
                        $this->_Value = $this->_Connect->hIncrByFloat($key, $field, $value);
                    } else {
                        $this->_Value = $this->_Connect->hIncrBy($key, $field, intval($value));
                    }
                    if ($this->_Value === "nil")
                        $this->_Value = null;
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
     * 获取hash元素对象全部字段名(域)
     * @access public
     * @param string $key 索引元素对象键
     * @return object
     */
    function fields($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hKeys($key);
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
     * 获取hash元素对象字段内容（域）长度
     * @access public
     * @param string $key 索引元素对象键s
     * @return object
     */
    function len($key)
    {
        try{
            if($this->_Connect->exists($key)) {
                $this->_Value = $this->_Connect->hLen($key);
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
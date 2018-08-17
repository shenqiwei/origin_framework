<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 16:10
 */

namespace Origin\Kernel\Data\Redis;


class HLL
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
     * 将元素对象参数添加到HyperLogLog 数据结构中
     * @access public
     * @param string $key 索引元素对象键
     * @param mixed $value 写入值
     * @return object
     */
    function add($key,$value)
    {
        try{
            $this->_Value = $this->_Connect->pfAdd($key,$value);
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
     *  计算HyperLogLog 的元素对象基数,或综合
     * @access public
     * @param mixed $key 索引元素对象键
     * @return object
     */
    function count($key)
    {
        try{
            if($this->_Connect->exists($key) or is_array($key)){
                $this->_Value = $this->_Connect->pfCount($key);
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
     * 多个 HyperLogLog 合并为一个 HyperLogLog
     * @access public
     * @param string $new 合成后HLL序列
     * @param array $key HLL原始序列集合
     * @return object
     */
    function merge($new,$key)
    {
        try{
            if(is_array($key)){
                $this->_Value = $this->_Connect->pfMerge($new,$key);
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
<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2018/8/17
 * Time: 16:12
 */

namespace Origin\Kernel\Data\Redis;


class Transaction
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
     * 取消事务，放弃执行事务块内的所有命令
     * @access public
     * @return object
     */
    function disable()
    {
        try{
            $this->_Value = $this->_Connect->discard();
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
     * 执行所有事务块内的命令
     * @access public
     * @return object
     */
    function execute()
    {
        try{
            $this->_Value = $this->_Connect->exec();
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
     * 标记一个事务块的开始
     * @access public
     * @return object
     */
    function multi()
    {
        try{
            $this->_Value = $this->_Connect->multi();
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
     * 取消 WATCH 命令对所有 key 的监视
     * @access public
     * @return object
     */
    function unWatch()
    {
        try{
            $this->_Value = $this->_Connect->unwatch();
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
     * 监视一个(或多个) key
     * @access public
     * @param mixed $key 事务标记
     * @return object
     */
    function watch($key)
    {
        try{
            $this->_Value = $this->_Connect->watch($key);
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
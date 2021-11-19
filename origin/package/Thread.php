<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin单一化事务线程处理功能封装 (该功能尽在控制台条件下生效，php ver 7.2+)
 */
namespace Origin\Package;

use parallel\Runtime;
use parallel\Channel;

abstract class Thread
{
    /**
     * @access protected
     * @var array $Array 预存变量数组
     * @var object $Thread 执行线程对象
    */
    protected $Array;
    protected $Thread;

    /**
     * 传入值
     * @access public
     * @param string $name 传入值名称
     * @param mixed $value 传入值
     * @return void
     */
    abstract function set($name,$value);

    /**
     * 获取值内容
     * @access public
     * @return void
     */
    abstract function get($name);

    /**
     * 拦截器
     * @access public
     * @param string $name 拦截器名称
     * @param string $function 拦截器方法
     * @param array $param 拦截器参数
     * @return void
     */
    abstract function filter($name,$function,$param);

    /**
     * 执行函数，该函数用于封装操作内容
     * @access public
     * @param object $channel 通道对象
     * @return void
     */
    abstract function action($channel);

    /**
     * 线程主执行函数
     * @access public
     * @static
     * @param object $object 执行对象
     * @return mixed 返回执行结果
    */
    function parallel($object)
    {
        # 创建返回值变量
        $receipt = null;
        # 声明线程对象
        $thread = new Runtion;
        # 声明通道对象
        $channel = new Channel;
        # 执行线程
        $future = $thread->run(function () use ($object, $channel) {
            $object->action($channel);
        });
        # 获取执行后内容
        $receipt = $channel->recv();
        # 判断线程状态
        if ($future->done)
            $thread->close();
        return $receipt;
    }
}
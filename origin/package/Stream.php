<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin信息流功能封装
 */
namespace Origin\Package;

class Stream
{
    /**
     * @access protected
     * @var resource $Stream 信息流源
     */
    protected $Stream;

    /**
     * @access protected
     * @var resource $Socket 套字节服务端源
     */
    protected $Socket;

    /**
     * @access protected
     * @var resource $Client 套字节客户端源
     */
    protected $Client;

    /**
     * @access protected
     * @var resource $Accept 接受信息源
     */
    protected $Accept;

    /**
     * @access protected
     * @var string $ErrCode 错误信息编码
     */
    protected $ErrCode;

    /**
     * @access protected
     * @var string $Error 错误信息
     */
    protected $Error = null;

    /**
     * 创建上下文信息流
     * @access public
     * @param array $option 操作数组，默认值 array() <空数组>
     * @param array|null $params 参数数组，默认值 null <空>
     * @return void
     */
    function context($option = array(), $params = null)
    {
        $this->Stream = stream_context_create($option, $params);
    }

    /**
     * 创建套字节信息流
     * @access public
     * @param string $addr 地址信息（ipv4|unix）
     * @param int|null $flag 设置协议类型，默认值 null,（STREAM_SERVER_BIND<UDP>|STREAM_SERVER_LISTEN）
     * @param boolean $context 接入流，默认值 false<不接入>，true 接入
     * @return void
     */
    function socket($addr, $flag = null, $context = false)
    {
        if ($context)
            $this->Socket = stream_socket_server($addr, $this->ErrCode, $this->Error, $flag, $this->Stream);
        else
            $this->Socket = stream_socket_server($addr, $this->ErrCode, $this->Error, $flag);
    }

    /**
     * 创建客户端连接
     * @access public
     * @param string $addr 地址信息（ipv4|unix）
     * @param float|null $cycle 等待时间，默认值 null
     * @param int|null $flag 设置协议类型，默认值 STREAM_CLIENT_CONNECT
     * STREAM_CLIENT_CONNECT 客户端连接流
     * STREAM_CLIENT_ASYNC_CONNECT 客户端异步连接流
     * STREAM_CLIENT_PERSISTENT 客户端持续连接流
     * @param boolean $context 接入流，默认值 false<不接入>，true 接入
     * @return void
     */
    function client($addr, $cycle=null, $flag=STREAM_CLIENT_CONNECT, $context=false)
    {
        if($context)
            $this->Client = stream_socket_client($addr,$this->ErrCode,$this->Error,$cycle,$flag,$this->Stream);
        else
            $this->Client = stream_socket_client($addr,$this->ErrCode,$this->Error,$cycle,$flag);
    }

    /**
     * 接受套字节服务端连接
     * @access public
     * @param string $socketName 接入对象名称
     * @param float|null $cycle 等待时间，默认值 null
     * @return resource 返回服务连接源
    */
    function accept(&$socketName, $cycle=null)
    {
        if(is_null($cycle))
            $cycle = floatval(ini_get("default_socket_timeout"));
        return $this->Accept = stream_socket_accept($this->Socket,$cycle,$socketName);
    }

    /**
     * 获取信息，或略连接状态
     * @access public
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 0
     * STREAM_OOB 处理超出边界的数据
     * STREAM_PEEK 从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * @param string|null $addr 返回请求地址信息
     * @return false|string 字节内容或失败状态
     */
    function recv($max=1024, $flag=MSG_DONTWAIT, &$addr=null)
    {
        return stream_socket_recvfrom($this->Socket,$max,$flag,$addr);
    }

    /**
     * 发送信息，或略连接状态
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $flag 获取方式，默认值 STREAM_OOB
     * STREAM_OOB    发送带外数据
     * @param string|null $addr 变更发送信息地址
     * @return int 返回传输字节长度
     */
    function send($buffer,  $flag=STREAM_OOB, $addr=null)
    {
        return stream_socket_sendto($this->Client,$buffer,$flag,$addr);
    }

    /**
     * 注册过滤器
     * @access public
     * @param string $name 过滤器注册名称
     * @param string $class 过滤器类名称（class name）
     * @return boolean 返回执行状态值
    */
    function filter($name,$class)
    {
        return stream_filter_register($name,$class);
    }

    /**
     * 设置指定信息流的过滤器
     * @access public
     * @param string $name 过滤器注册名称
     * @param resource|null $resource 过滤器注册名称，默认值 null，调用stream server流
     * @param int $type 过滤对象类型，默认值  STREAM_FILTER_ALL <过滤全部操作>
     * STREAM_FILTER_READ 过滤获取操作
     * STREAM_FILTER_WRITE 过滤返送操作
     * STREAM_FILTER_ALL
     * @param mixed $param 参数
     * @return resource 返回服务连接源
     */
    function filterAp($name, $resource=null, $type=STREAM_FILTER_ALL,$param=null)
    {
        if(is_null($resource))
            $resource = $this->Stream;
        return stream_filter_append($resource,$name,$type,$param);
    }

    /**
     * 设置指定信息流的过滤器
     * @access public
     * @param string $name 过滤器注册名称
     * @param resource|null $resource 过滤器注册名称，默认值 null，调用stream server流
     * @param int $type 过滤对象类型，默认值  STREAM_FILTER_ALL <过滤全部操作>
     * STREAM_FILTER_READ 过滤获取操作
     * STREAM_FILTER_WRITE 过滤返送操作
     * STREAM_FILTER_ALL
     * @param mixed $param 参数
     * @return resource 返回服务连接源
     */
    function filterPre($name, $resource=null, $type=STREAM_FILTER_ALL,$param=null)
    {
        if(is_null($resource))
            $resource = $this->Stream;
        return stream_filter_prepend($resource,$name,$type,$param);
    }

    /**
     * 删除已注册过滤器
     * @access public
     * @param resource|null $resource $resource 过滤器注册名称，默认值 null，调用stream server流
     * @return boolean 返回执行结果状态值
     */
    function filterRe($resource=null)
    {
        if(is_null($resource))
            $resource = $this->Stream;
        return stream_filter_remove($resource);
    }

    /**
     * 关闭客户端连接源
     * @access public
     * @return boolean 返回执行结果状态值
     */
    function close()
    {
        return fclose($this->Client);
    }

    /**
     * 关闭客户端连接源
     * @access public
     * @return boolean 返回执行结果状态值
     */
    function cancel()
    {
        return fclose($this->Socket);
    }

    /**
     * 注销套字节连接源
     * @access public
     * @param int $type 注销方式默认值 STREAM_SHUT_RDWR， STREAM_SHUT_RD(中断接受操作)，STREAM_SHUT_WR(中断发送操作)，STREAM_SHUT_RDWR(中断所有操作)
     * @return boolean 返回执行结果状态值
     */
    function shutdown($type=STREAM_SHUT_RDWR)
    {
        return stream_socket_shutdown($this->Socket,$type);
    }

    /**
     * 获取错误信息
     * @access public
     * @return string|null 返回异常信息
    */
    function getError()
    {
        return $this->Error;
    }
}
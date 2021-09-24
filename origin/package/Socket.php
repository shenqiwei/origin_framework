<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context Origin通信封装
 */
namespace Origin\Package;

class Socket
{
    /**
     * @acceess protected
     * @var resource $Socket 套字节源
     */
    protected $Socket;

    /**
     * @acceess protected
     * @var string $IP IP地址信息
     */
    protected $IP;

    /**
     * @acceess protected
     * @var string $IPType IP类型
     */
    protected $IPType;

    /**
     * @acceess protected
     * @var int $IPDomain 访问域名类型
     */
    protected $IPDomain;

    /**
     * @acceess protected
     * @var int $Port 端口号
     */
    protected $Port;

    /**
     * @acceess protected
     * @var int $ErrCode 错误代码
     */
    protected $ErrCode;

    /**
     * @acceess protected
     * @var string|null $Error 错误信息
    */
    protected $Error=null;

    /**
     * 构造函数，预创建套字节
     * @access public
     * @param string $ip ip地址（ipv4|ipv6）
     * @param int $port 端口号，默认值 0 <无效端口号>，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return void
    */
    function __construct($ip,$port=0)
    {
        $this->IP = $ip;
        $_validate = new Validate();
        if($_validate->_ipv4($ip)){
            $this->IPType = AF_INET;
            $this->IPDomain = 1;
        }elseif($_validate->_ipv6($ip)){
            $this->IPType = AF_INET6;
            $this->IPDomain = 2;
        }else{
            $this->IPType= AF_UNIX;
            $this->IPDomain = 0;
        }
        if(intval($port) > 0)
            $this->Port = intval($port);
    }

    /**
     * 创建TCP连接
     * @access public
     * @return void
    */
    function tcp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_STREAM,SOL_TCP);
    }

    /**
     * 创建UDP连接
     * @access public
     * @return void
     */
    function udp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_DGRAM,SOL_UDP);
    }

    /**
     * 创建数据包连接
     * @access public
     * @return void
     */
    function packet()
    {
        $this->Socket = socket_create($this->IPType,SOCK_SEQPACKET,0);
    }

    /**
     * 创建icmp连接
     * @access public
     * @return void
     */
    function icmp()
    {
        $this->Socket = socket_create($this->IPType,SOCK_RAW,0);
    }

    /**
     * 创建远程部署管理连接
     * @access public
     * @return void
     */
    function rdm()
    {
        $this->Socket = socket_create($this->IPType,SOCK_RDM,0);
    }

    /**
     * 建立连接，函数（IPV4，UNIX）默认对该连接地址进行名称绑定,若绑定失败，可以在getError函数中获取错误信息
     * @access public
     * @return boolean 返回服务连接状态
    */
    function connect()
    {
        if($this->IPDomain === 1 and $this->IPDomain === 0){
            socket_bind($this->Socket,$this->IP,$this->Port);
        }
        return socket_connect($this->Socket,$this->IP,$this->Port);
    }

    /**
     * 获取套字节连接
     * @access public
     * @return resource 返回链接源
    */
    function accept()
    {
        return socket_accept($this->Socket);
    }

    /**
     * 执行监听，该函数仅在使用 tcp(),packet()函数生效
     * @access public
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean 返回监听状态
    */
    function listen($max=1024)
    {
        return socket_listen($this->Socket,$max);
    }

    /**
     * 标注新的监听端口，创建连接
     * @access public
     * @param int $port 端口号，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean 返回监听状态
    */
    function newListen($port, $max=1024)
    {
        return socket_create_listen($port,$max);
    }

    /**
     * 连接阻塞状态
     * @access public
     * @param int $status 状态默认值 0：非阻塞，1：阻塞
     * @return boolean 返回设置状态
    */
    function block($status=0)
    {
        if($status === 1)
            return socket_set_block($this->Socket);
        else
            return socket_set_nonblock($this->Socket);
    }

    /**
     * 读取数据
     * @access public
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return string|false 返回读取内容或失败状态
    */
    function read($max=1024)
    {
        return socket_read($this->Socket, $max);
    }

    /**
     * 写入数据
     * @access public
     * @param string $string 发送内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @return boolean 返回写入状态
    */
    function write($string, $max=1024)
    {
        return socket_write($this->Socket,$string,$max);
    }

    /**
     * 获取信息
     * @access public
     * @param string $buffer 缓冲变量
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTWAIT
     * MSG_OOB	处理超出边界的数据
     * MSG_PEEK	从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL	在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT	如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @return int 返回字节数
    */
    function recv(&$buffer, $max=1024, $flag=MSG_DONTWAIT)
    {
        return socket_recv($this->Socket,$buffer,$max,$flag);
    }

    /**
     * 获取信息，忽略连接状态
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTWAIT
     * MSG_OOB    处理超出边界的数据
     * MSG_PEEK    从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL    在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT    如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @param string|null $addr 地址信息（ipv4|unix），默认值 null
     * @param int $port 端口号，默认值 0，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return int 返回字节数
     */
    function recvf(&$buffer, $max=1024, $flag=MSG_DONTWAIT, $addr=null, $port=0)
    {
        return socket_recvfrom($this->Socket,$buffer, $max, $flag,$addr,$port);
    }

    /**
     * 获取信息
     * @access public
     * @param array $buffer 缓冲变量，获取内容
     * @param int $flag 获取方式，获取内容，默认值 MSG_DONTWAIT
     * MSG_OOB    处理超出边界的数据
     * MSG_PEEK    从接受队列的起始位置接收数据，但不将他们从接受队列中移除。
     * MSG_WAITALL    在接收到至少 len 字节的数据之前，造成一个阻塞，并暂停脚本运行（block）。但是， 如果接收到中断信号，或远程服务器断开连接，该函数将返回少于 len 字节的数据。
     * MSG_DONTWAIT    如果制定了该flag，函数将不会造成阻塞，即使在全局设置中指定了阻塞设置。
     * @return int 返回字节数
     */
    function recvm(&$buffer, $flag=MSG_DONTWAIT)
    {
        return socket_recvmsg($this->Socket, $buffer,$flag);
    }

    /**
     * 发送信息
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB	发送带外数据
     * MSG_EOR	标出一个记录标记。发送的数据完成记录。
     * MSG_EOF	关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @return int 返回字节数
    */
    function send($buffer, $max=1024, $flag=MSG_DONTROUTE)
    {
        return socket_send($this->Socket,$buffer,$max,$flag);
    }

    /**
     * 发送信息,或略连接状态
     * @access public
     * @param string $buffer 缓冲变量，获取内容
     * @param int $max 最大传递数量(byte)，默认值 1024 (1kb)
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB	发送带外数据
     * MSG_EOR	标出一个记录标记。发送的数据完成记录。
     * MSG_EOF	关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @param string|null $addr 地址信息（ipv4|unix），默认值 null
     * @param int $port 端口号，默认值 0，1-1024服务端口<非必要，请勿占用>，1025-65535<可定义端口>
     * @return int 返回字节数
     */
    function sendf($buffer, $max=1024, $flag=MSG_DONTROUTE, $addr=null, $port=0)
    {
        return socket_sendto($this->Socket,$buffer,$max,$flag,$addr,$port);
    }

    /**
     * 发送信息
     * @access public
     * @param array $buffer 缓冲变量，获取内容
     * @param int $flag 获取方式，默认值 MSG_DONTROUTE
     * MSG_OOB	发送带外数据
     * MSG_EOR	标出一个记录标记。发送的数据完成记录。
     * MSG_EOF	关闭套接字的发送方端，并在发送的数据的末尾包含相应的通知。发送的数据完成事务。
     * MSG_DONTROUTE 绕过路由，使用直接接口。
     * @return int 返回字节数
     */
    function sendm($buffer,  $flag=MSG_DONTROUTE)
    {
        return socket_sendmsg($this->Socket,$buffer,$flag);
    }

    /**
     * 注销行为
     * @access public
     * @param int $type 默认值 2，注销类型 0：注销读取行为，1：注销写入行为，2：注销全部行为
     * @return boolean 返回执行状态值
     */
    function shutdown($type=2)
    {
        if($type === 1 or $type === 2)
            $_type = $type;
        else
            $_type = 2;
        return socket_shutdown($this->Socket,$_type);
    }

    /**
     * 关闭套字节请求
     * @access public
     * @return void
     */
    function close()
    {
        socket_close($this->Socket);
    }

    /**
     * 清空错误信息
     * @access public
     * @return void
    */
    function clear()
    {
        socket_clear_error($this->Socket);
    }

    /**
     * 获取错误信息
     * @access public
     * @return string|null 返回异常信息
    */
    function getError()
    {
        $this->ErrCode = socket_last_error();
        $this->Error = socket_strerror($this->ErrCode);
        return $this->Error;
    }
}
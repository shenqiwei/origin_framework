<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Kernel.Parameter.Socket *
 * version: 1.0 *
 * structure: common framework *
 * email: cheerup.shen@foxmail.com *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * create Time: 2017/01/06 14:30
 * update Time: 2017/01/09 16:22
 * chinese Context: IoC Socket通信(TCP)
 */
namespace Origin\Kernel\Protocol;

class Socket
{
    /**
     * @var object $_Socket
     * 通信连接对象
    */
    protected $_Socket = null;
    /**
     * @var boolean $_Boolean
     * 返回执行状态
    */
    protected $_Boolean = true;
    /**
     * @var string $_Value
     * 返回内容信息
    */
    protected $_Value = null;
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    # 构造方法
    function __construct()
    {
        # 创建socket连接对象
        $this->_Socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);

    }
    /**
     * 回传类对象信息
     * @access public
     * @param object $object
     */
    function __setObj($object)
    {
        $this->_Object = $object;
    }
    /**
     * 获取类对象信息,仅类及其子类能够使用
     * @access public
     * @return object
     */
    protected function __getObj()
    {
        return $this->_Object;
    }
    /**
     * Socket服务端链接函数
     * @access public
     * @throws
     * @param string $connect_ip 链接地址
     * @param int $connect_port 链接接口
     * @param int $listen_len 监听数据长度
     * @return object
     */
    function service($connect_ip=null,$connect_port=0,$listen_len=1024)
    {
        # 设定接受对象
        if(!socket_bind($this->_Socket,$connect_ip,intval($connect_port)))
            throw new \Exception("Socket bind setup failed");
        # 调用监听方法
        if(!socket_listen($this->_Socket,$listen_len))
            throw new \Exception("Socket listen setup failed");
        # 调用返回内容长度
        if(!socket_accept($this->_Socket))
            throw new \Exception("Socket accept ");
        return $this->_Object;
    }
    /**
     * Socket客户端链接函数
     * @access public
     * @throws
     * @param string $connect_ip 链接地址
     * @param string $connect_port 链接接口
     * @return mixed
     */
    function client($connect_ip,$connect_port)
    {
        if(!socket_connect($this->_Socket,$connect_ip,intval($connect_port)))
            throw new \Exception("Socket connection has error");
        return $this->_Object;
    }
    /**
     * Socket信息读取函数
     * @access publi
     * @param int $length
     * @throws \Exception
     * @return object
    */
    function read($length)
    {
        if(!$_msg = socket_read($this->_Socket,intval($length)))
            throw new \Exception(socket_last_error($this->_Socket));
        return $this->_Socket;
    }
    /**
     * Socket信息发送函数
     * @access public
     * @param string $msg 发送信息
     * @throws \Exception
     * @return object
    */
    function write($msg)
    {
        if(!socket_write($this->_Socket,$msg,strlen($msg)))
            throw new \Exception(socket_last_error($this->_Socket));
        return $this->_Socket;
    }
    /**
     * 获取内容值
     * @access public
     * @return mixed
    */
    function getValue()
    {
        return $this->_Value;
    }
    /**
     * 获取错误信息
     * @access public
     * @return mixed
    */
    function getErrMsg()
    {
        return socket_last_error($this->_Socket);
    }
    # 析构方法
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        socket_close($this->_Socket);
    }
}
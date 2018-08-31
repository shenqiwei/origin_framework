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
namespace Ring\Kernel\Protocol;

class Socket
{
    /**
     * @var object $_Socket
     * 通信连接对象
    */
    private $_Socket = null;
    /**
     * @var boolean $_Boolean
     * 返回执行状态
    */
    private $_Boolean = true;
    /**
     * @var string $_Value
     * 返回内容信息
    */
    private $_Value = null;
    /**
     * @var object $_Object
     * 数据库对象，有外部实例化之后，装在进入对象内部，进行再操作
     */
    protected $_Object = null;
    # 构造方法
    function __construct()
    {
        try{
            # 创建socket连接对象
            $this->_Socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
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
     * 切换绑定地址信息
     * @access public
     * @param @param string $connect_ip
     * @param int $connect_port
     * @return object
    */
    function bind($connect_ip,$connect_port)
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Boolean = socket_bind($this->_Socket,$connect_ip,$connect_port);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    /**
     * 执行socket连接
     * @access public
     * @param string $connect_ip
     * @param int $connect_port
     * @return object
    */
    function connect($connect_ip,$connect_port)
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Boolean = socket_connect($this->_Socket,$connect_ip,$connect_port);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    /**
     * 监听socket连接
     * @access public
     * @param int $listen_count 监听套字数量
     * @return object
    */
    function listen($listen_count=0)
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Boolean = socket_listen($this->_Socket,$listen_count);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    /**
     * 接受socket连接
     * @access public
     * @return object
    */
    function accept()
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Boolean = socket_accept($this->_Socket);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    /**
     * 读取客户端信息
     * @access public
     * @param int $len 获取信息长度
     * @return object
    */
    function read($len)
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Value = socket_read($this->_Socket,$len);
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    /**
     * 发送数据信息
     * @access public
     * @param string $msg 发送信息
     * @return object
    */
    function write($msg)
    {
        try{
            # 设置连接对象ip及端口信息
            $this->_Value = socket_write($this->_Socket,$msg,strlen($msg));
        }catch (\Exception $e){
            var_dump(debug_backtrace(0,1));
            echo("<br />");
            echo('Origin (Query) Class Error: '.$e->getMessage());
            exit(0);
        }
        return $this->_Object;
    }
    # 析构方法
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        socket_close($this->_Socket);
    }
}
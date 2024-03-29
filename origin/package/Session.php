<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin服务器会话
 */
namespace Origin\Package;

class Session
{
    /**
     * @access public
     * @context 操作常量
     */
    const SESSION_ID = "id"; # 获取session_id
    const SESSION_CLOSE = "unset"; # 注销session会话
    const SESSION_CLEAR = "destroy"; # 清空session会话
    const SESSION_RELOAD = "regenerate"; # 重置session_id并保留原id
    const SESSION_RST = "rest"; # 重置session内值
    const SESSION_DEL = "delete"; # 删除session的值
    const SESSION_ENC = "encode"; # 编码session信息
    const SESSION_DEC = "decode"; # 解码session信息

    /**
     * 构造函数
     * @access public
     * @return void
    */
    function __construct()
    {
        if(!ini_get('session.auto_start')) session_start();
    }

    /**
     * session会话设置
     * @access public
     * @param string $option 设置项
     * @param string|null $key 会话键名
     * @return false|string|null 返回会话执行结果内容或失败状态
    */
    function edit($option,$key=null)
    {
        $receipt = null;
        if($option == self::SESSION_ID) $receipt = session_id();
        if($option == self::SESSION_CLOSE) session_unset();
        if($option == self::SESSION_CLEAR) session_destroy();
        if($option == self::SESSION_RELOAD) session_regenerate_id(false);
        if($option == self::SESSION_RST) session_reset();
        if($option == self::SESSION_DEL)
            if(isset($_SESSION[$key])) unset($_SESSION[$key]);
        if($option == self::SESSION_ENC) session_encode();
        if($option == self::SESSION_DEC)
            if(isset($_SESSION[$key])) session_decode($key);
        return $receipt;
    }

    /**
     * 创建会话值内容
     * @access public
     * @param string $key 会话键名
     * @param mixed $value 值
     * @return void
    */
    function set($key,$value)
    {
        # 判断session传入参数是否有名称分割符号
        if(strpos($key, '.'))
            $key = array_filter(explode('.', $key));
        # 判断当前参数是否为数组，如果不是直接执行session操作
        if(is_array($key)){
            if(count($key) > 3){
                # 异常提示：session无法支持超过3个维度的数组结构
                try{
                    throw new \Exception('Origin Support Error: Session can support more than three dimensional array structure');
                }catch(\Exception $e){
                    errorLog($e->getMessage());
                    exception("Session Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }else{
                $array_key = array_keys($key);
                # 符合维度要求
                if(count($key) == 3){
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[$array_key[0]]][$key[$array_key[1]]][$key[$array_key[2]]] = stripslashes($value);
                }elseif(count($key) == 2){
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[$array_key[0]]][$key[$array_key[1]]] = stripslashes($value);
                }else{
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[0]] = stripslashes($value);
                }
            }
        }else{
            # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
            $_SESSION[$key] = stripslashes($value);
        }
    }

    /**
     * 获取会话值内容
     * @access public
     * @param string $key 会话键名
     * @return mixed 返回会话存储值
    */
    function get($key)
    {
        $receipt = null;
        # 判断session传入参数是否有名称分割符号
        if(strpos($key, '.'))
            $key = array_filter(explode('.', $key));
        # 判断当前参数是否为数组，如果不是直接执行session操作
        if(is_array($key)){
            if(count($key) > 3){
                # 异常提示：session无法支持超过3个维度的数组结构
                try{
                    throw new \Exception('Origin Support Error: Session can support more than three dimensional array structure');
                }catch(\Exception $e){
                    errorLog($e->getMessage());
                    exception("Session Error",$e->getMessage(),debug_backtrace(0,1));
                    exit();
                }
            }else{
                $array_key = array_keys($key);
                # 符合维度要求
                if(count($key) == 3){
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[0]][$key[1]][$key[2]]))
                        $receipt = $_SESSION[$key[$array_key[0]]][$key[$array_key[1]]][$key[$array_key[2]]];
                }elseif(count($key) == 2){
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[$array_key[0]]][$key[$array_key[1]]]))
                        $receipt = $_SESSION[$key[$array_key[0]]][$key[$array_key[1]]];
                }else{
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[$array_key[0]]]))
                        $receipt = $_SESSION[$key[$array_key[0]]];
                }
            }
        }else{
            if(isset($_SESSION[$key]))
                $receipt = $_SESSION[$key];
        }
        return $receipt;
    }

    /**
     * 析构函数
     * @access public
     * @return void
     */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!ini_get('session.auto_start')) session_commit();
    }
}
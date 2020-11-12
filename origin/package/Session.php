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
     * @context 构造函数
    */
    function __construct()
    {
        if(!ini_get('session.auto_start')) session_start();
    }
    /**
     * @access public
     * @param string $option 设置项
     * @param string $key 会话键名
     * @return mixed
     * @context session会话设置
    */
    function edit($option,$key=null)
    {
        $_receipt = null;
        # 获取session_id
        if($option == 'id') $_receipt = session_id();
        # 注销session会话
        if($option == 'unset') session_unset();
        # 清空session会话
        if($option == 'destroy') session_destroy();
        # 重置session_id并保留原id
        if($option == 'regenerate') session_regenerate_id(false);
        # 重置session内值
        if($option == 'reset') session_reset();
        # 删除session的值
        if($option == 'delete')
            if(isset($_SESSION[$key])) unset($_SESSION[$key]);
        # 编码session信息
        if($option == 'encode') session_encode();
        # 解码session信息
        if($option == 'decode'){
            if(isset($_SESSION[$key])) session_decode($key);
        }
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 会话键名
     * @param mixed $value 值
     * @return null
     * @context 创建会话值内容
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
                $_array_key = array_keys($key);
                # 符合维度要求
                if(count($key) == 3){
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]][$key[$_array_key[2]]] = stripslashes($value);
                }elseif(count($key) == 2){
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]] = stripslashes($value);
                }else{
                    # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
                    $_SESSION[$key[0]] = stripslashes($value);
                }
            }
        }else{
            # 当值参数不等于null时，则修改当前session会话内容，并对内容进行转码
            $_SESSION[$key] = stripslashes($value);
        }
        return null;
    }
    /**
     * @access public
     * @param string $key 会话键名
     * @return mixed
     * @context 获取会话值内容
    */
    function get($key)
    {
        $_receipt = null;
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
                $_array_key = array_keys($key);
                # 符合维度要求
                if(count($key) == 3){
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[0]][$key[1]][$key[2]]))
                        $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]][$key[$_array_key[2]]];
                }elseif(count($key) == 2){
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]]))
                        $_receipt = $_SESSION[$key[$_array_key[0]]][$key[$_array_key[1]]];
                }else{
                    # 当参数值为空时，判断session会话是否存在，如果存在将session值内容赋入返回值中，反之返回null
                    if(isset($_SESSION[$key[$_array_key[0]]]))
                        $_receipt = $_SESSION[$key[$_array_key[0]]];
                }
            }
        }else{
            if(isset($_SESSION[$key]))
                $_receipt = $_SESSION[$key];
        }
        return $_receipt;
    }
    /**
     * @access public
     * @context 析构函数
     */
    function __destruct()
    {
        // TODO: Implement __destruct() method.
        if(!ini_get('session.auto_start')) session_commit();
    }
}
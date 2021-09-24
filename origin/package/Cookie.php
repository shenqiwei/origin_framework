<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2020
 * @context: Origin浏览器会话
 */
namespace Origin\Package;

class Cookie
{
    /**
     * 构造函数，激活会话服务
     * @access public
     * @return void
     */
    function __construct()
    {
        if(!ini_get('session.auto_start')) session_start();
    }

    /**
     * cookie会话设置
     * @access public
     * @param string $option 设置项
     * @param string $value 会话值
     * @return false|string|null 返回操作结果
     */
    function edit($option,$value)
    {
        $_receipt = null;
        if(is_null($value))
            ini_set('session.'.strtolower($option), $value);
        else
            $_receipt = ini_get('session.'.strtolower($option));
        return $_receipt;
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
        setcookie($key, $value, config('COOKIE_LIFETIME'),  config('COOKIE_PATH'),  config('COOKIE_DOMAIN'));
    }

    /**
     * 获取会话值内容
     * @access public
     * @param string $key 会话键名
     * @return mixed 返回存储值
     */
    function get($key)
    {
        return $_COOKIE[$key];
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
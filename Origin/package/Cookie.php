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
     * @access public
     * @param string $option 设置项
     * @param string $value 会话值
     * @return mixed
     * @context cookie会话设置
     */
    static function edit($option,$value)
    {
        $_receipt = null;
        if(is_null($value))
            ini_set('session.'.strtolower($option), $value);
        else
            $_receipt = ini_get('session.'.strtolower($option));
        return $_receipt;
    }
    /**
     * @access public
     * @param string $key 会话键名
     * @param mixed $value 值
     * @context 创建会话值内容
     */
    static function set($key,$value)
    {
        setcookie($key, $value, config('COOKIE_LIFETIME'),  config('COOKIE_PATH'),  config('COOKIE_DOMAIN'));
    }
    /**
     * @access public
     * @return mixed
     * @context 获取会话值内容
     */
    static function get()
    {
        return session_get_cookie_params();
    }
}
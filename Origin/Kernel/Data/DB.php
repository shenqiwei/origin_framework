<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0.1
 * @copyright 2015-2019
 * @context: IoC Mysql封装类
 */
namespace Origin\Kernel\Data;

class DB
{
    /**
     * Mysql数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function mysql($connect_name=null)
    {
        $_dao = new Mysql($connect_name);
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * Redis数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function redis($connect_name=null)
    {
        /**
         * 调用Redis数据库核心包
         */
        $_dao = new Redis($connect_name);
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * MongoDB数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function mongodb($connect_name=null)
    {
        $_dao = new Mongodb($connect_name);
        $_dao->__setSQL($_dao);
        return $_dao;
    }
}
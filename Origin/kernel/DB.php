<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0.1
 * @copyright 2015-2019
 * @context: Origin框架Mysql封装类
 */
namespace Origin\Kernel;

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
        $_dao = new Database($connect_name,"mysql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * PostgreSQL数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function pgsql($connect_name=null)
    {
        $_dao = new Database($connect_name,"pgsql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * SQL server数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function mssql($connect_name=null)
    {
        $_dao = new Database($connect_name,"mssql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * sqlite数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function sqlite($connect_name=null)
    {
        $_dao = new Database($connect_name,"sqlite");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * SQL server数据库操作方法
     * @access public
     * @param string $connect_name 链接名
     * @return object
     */
    static function oracle($connect_name=null)
    {
        $_dao = new Database($connect_name,"oracle");
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
        return new Redis($connect_name);
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
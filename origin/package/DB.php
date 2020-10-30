<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0.1
 * @copyright 2015-2019
 * @context: Origin框架Mysql封装类
 */
namespace Origin\Package;

class DB
{
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context Mysql数据库操作方法
     */
    static function mysql($connect_name=null)
    {
        $_dao = new Database($connect_name,"mysql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context PostgreSQL数据库操作方法
     */
    static function pgsql($connect_name=null)
    {
        $_dao = new Database($connect_name,"pgsql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context SQL server数据库操作方法
     */
    static function mssql($connect_name=null)
    {
        $_dao = new Database($connect_name,"mssql");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context sqlite数据库操作方法
     */
    static function sqlite($connect_name=null)
    {
        $_dao = new Database($connect_name,"sqlite");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context SQL server数据库操作方法
     */
    static function oracle($connect_name=null)
    {
        $_dao = new Database($connect_name,"oracle");
        $_dao->__setSQL($_dao);
        return $_dao;
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context Redis数据库操作方法
     */
    static function redis($connect_name=null)
    {
        # 调用Redis数据库核心包
        return new Redis($connect_name);
    }
    /**
     * @access public
     * @param string $connect_name 链接名
     * @return object
     * @context MongoDB数据库操作方法
     */
    static function mongodb($connect_name=null)
    {
        $_dao = new Mongodb($connect_name);
        $_dao->__setSQL($_dao);
        return $_dao;
    }
}
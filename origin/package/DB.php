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
     * Mysql数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回mysql数据源连接对象
     */
    static function mysql($connect_name=null)
    {
        $dao = new Database($connect_name, Query::RESOURCE_TYPE_MYSQL);
        $dao->__setSQL($dao);
        return $dao;
    }

    /**
     * PostgreSQL数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回pgsql数据源连接对象
     */
    static function pgsql($connect_name=null)
    {
        $dao = new Database($connect_name, Query::RESOURCE_TYPE_PGSQL);
        $dao->__setSQL($dao);
        return $dao;
    }

    /**
     * SQL server数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回mssql数据源连接对象
     */
    static function mssql(?string $connect_name=null)
    {
        $dao = new Database($connect_name, Query::RESOURCE_TYPE_MSSQL);
        $dao->__setSQL($dao);
        return $dao;
    }

    /**
     * sqlite数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回sqlite数据源连接对象
     */
    static function sqlite(?string $connect_name=null)
    {
        $dao = new Database($connect_name, Query::RESOURCE_TYPE_SQLITE);
        $dao->__setSQL($dao);
        return $dao;
    }

    /**
     * Oracle数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回oracle数据源连接对象
     */
    static function oracle(?string $connect_name=null)
    {
        $dao = new Database($connect_name, Query::RESOURCE_TYPE_ORACLE);
        $dao->__setSQL($dao);
        return $dao;
    }

    /**
     * Redis数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回redis数据源连接对象
     */
    static function redis(?string $connect_name=null)
    {
        # 调用Redis数据库核心包
        return new Redis($connect_name);
    }

    /**
     * MongoDB数据库操作方法
     * @access public
     * @param string|null $connect_name 链接名
     * @return object 返回mongodb数据源连接对象
     */
    static function mongodb(?string $connect_name=null)
    {
        $dao = new Mongodb($connect_name);
        $dao->__setSQL($dao);
        return $dao;
    }
}
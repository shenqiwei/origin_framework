<?php
/**
 * coding: utf-8 *
 * system OS: windows2008 *
 * work Tools:Phpstorm *
 * language Ver: php7.1 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Ring.Door*
 * version: 0.1 *
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * @context:
 * IoC 单向入口操作文件
 */
# 版本控制
if((float)PHP_VERSION < 5.5) die('this program is support to lowest php version 5.5');
# DIRECTORY_SEPARATOR：PHP内建常量，用来返回当前系统文件夹连接符号LINUX（/）,WINNER（\）
# 路径分割符
if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);
# 主程序文件目录常量
if(!defined('ROOT')) define('ROOT',dirname(__DIR__).DS);
# 引述文件根地址
if(!defined("ROOT_ADDRESS")) define("ROOT_ADDRESS",dirname(__FILE__));
# 是否启用编码混成
if(!defined('MARK_RELOAD')) define('MARK_RELOAD',true);
# 协议类型
if(!defined("__PROTOCOL__")) define("__PROTOCOL__", isset($_SERVER["HTTPS"])? "https://" : "http://");
# 地址信息
if(!defined("__HOST__")) define("__HOST__",__PROTOCOL__.$_SERVER["HTTP_HOST"]."/");
# 调试状态常量
if(!defined('DEBUG')) define('DEBUG',false);
# 错误信息常量
if(!defined('ERROR')) define('ERROR',false);
# 错误信息显示
# E_ALL = 11 所有的错误信息
# E_ERROR = 1 报致命错误
# E_WARNING = 2 报警告错误
# E_NOTICE = 8 报通知警告
# E_ALL& ~E_NOTICE = 3 不报NOTICE错误, 常量参数 TRUE
# 0 不报错误，默认常量参数 FALSE
if(ERROR == true or ERROR == 3)
    error_reporting(E_ALL & ~E_NOTICE);
elseif(ERROR == 11)
    error_reporting(E_ALL);
elseif(ERROR == 1)
    error_reporting(E_ERROR);
elseif(ERROR == 2)
    error_reporting(E_WARNING);
elseif(ERROR == 8)
    error_reporting(E_NOTICE);
else error_reporting(0);
# 引入主方法文件
include('Method'.DS.'Function.php');
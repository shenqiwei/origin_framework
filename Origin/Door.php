<?php
/**
* coding: utf-8 *
* system OS: windows2008 *
* work Tools:Phpstorm *
* language Ver: php7.1 *
* agreement: PSR-1 to PSR-11 *
* filename: IoC.Ring.Door*
* version: 0.1 *
* structure: common framework *
* designer: 沈启威 *
* developer: 沈启威 *
* partner: 沈启威 *
* chinese Context:
* IoC 单向入口操作文件
** 升级计划，使用goto语句解决语句重叠问题，优化结构调用性能
 */
# 版本控制
if((float)PHP_VERSION < 5.5) die('this program is support to lowest php version 5.5');
# DIRECTORY_SEPARATOR：PHP内建常量，用来返回当前系统文件夹连接符号LINUX（/）,WINNER（\）
# 路径分割符
if(!defined('SLASH')) define('SLASH',DIRECTORY_SEPARATOR);
# 主程序文件目录常量
if(!defined('ROOT')) define('ROOT',dirname(__DIR__).SLASH);
# 功能设置常量
if(!defined('DEBUG')) define('DEBUG',FALSE);
if(!defined('ERROR')) define('ERROR',FALSE);
# 地址访问规则

# 开发目录常量

# 调试状态
# 原结构中使用debug结构嵌入工具类中，并将错误信息返回日志中，并将错误信息格式化放回到页面中

# 错误信息显示
# E_ALL = 11 所有的错误信息
# E_ERROR = 1 报致命错误
# E_WARNING = 2 报警告错误
# E_NOTICE = 8 报通知警告
# E_ALL& ~E_NOTICE = 3 不报NOTICE错误, 常量参数 TRUE
# 0 不报错误，默认常量参数 FALSE
if(ERROR == TRUE or ERROR == 3)
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
include('Method'.SLASH.'Function.php');
# 引入入口文件
include('Kernel'.SLASH.'Entrance.class.php');
# 启动入口文件
Origin\Kernel\Entrance::starting();
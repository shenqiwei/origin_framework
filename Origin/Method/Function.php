<?php
/**
 *  coding: utf-8  * 
 *  system OS: windows2008  * 
 *  work Tools:Phpstorm  * 
 *  language Ver: php7.1  * 
 *  agreement: PSR-1 to PSR-11  * 
 *  filename: IoC.Origin.Function.function * 
 *  version: 0.1 * 
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 */
# 初试公共函数包
include("Common/Common.php");
include("Common/Import.php");
include("Common/Config.php");
# 框架柱目录文件路径
if(!defined("RING")) define("RING", "Origin".DS);
# 公共配置常量
# 创建基础常量
# 默认应用访问目录，默认为空，当进行web开发时，区分前后台时，填入并在Apply下建立同名文件夹
if(!defined("__APPLICATION__")) define("__APPLICATION__", Configuration("DEFAULT_APPLICATION"));
# 插件应用常量
if(!defined("__PLUGIN__")) define("__PLUGIN__", Configuration("ROOT_PLUGIN"));
# 资源应用常量
if(!defined("__RESOURCE__")) define("__RESOURCE__", Configuration("ROOT_RESOURCE"));

# 加载函数封装类
Import("File:File"); # 文件控制类
Import("Parameter:Request"); # 调用请求控制器
Import("Parameter:Validate"); # 调用验证控制器
Import("Parameter:Filter");

# 基础操作方法包应用
include("Common/File.php");
include("Common/Request.php");
include("Common/Validate.php");
include("Common/Filter.php");
include("Common/Session.php");
include("Common/Cookie.php");
# 应用公共函数文件
include("Common/Log.php"); # 引用日志函数包
include("Common/Public.php"); # 文件操作函数包
# 公共应用函数类
Import("File:Upload"); # 文件上传控制类
Import("Data:Mysql"); # 调用Mysql数据库对象组件
Import("Data:Redis"); # 调用Redis数据库对象组件
Import("Data:Mongodb"); # 调用MongoDB 70+支持包
Import("Interface:Mark:Impl:Label"); # 调用内建标签解释结构接口控制类
Import("Mark:Label"); # 调用标签解析器控制类
Import("Export:Verify"); # 调用验证码组件
Import("Graph:View"); # 调用界面模板匹配控制类
Import("Parameter:Output"); # 调用数据结构输出控制类
# 引入路由控制函数包
Import("Protocol:Route"); # 调用路由控制函数包
Import("Protocol:Curl"); # 调用远程请求函数包
/**
 * Common 有行能bug内容，关系结构Config需要进行结构简化和重定义
*/
# 应用结构包调用
Common("Common:Public"); # 引入公共函数包
# 公共控制器文件
Import("Application:Controller");
# 动态加载文件
include("Common/Entrance.php"); # 引入入口文件包
// 调用方法体
Entrance();
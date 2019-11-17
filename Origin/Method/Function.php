<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 */
# 初始公共函数包
include("Common/Common.php");
include("Common/Loading.php");
include("Common/Config.php");
include("Common/initialize.php");
# 基础操作方法包应用
include("Common/File.php");
include("Common/Request.php");
include("Common/Validate.php");
include("Common/Filter.php");
include("Common/Session.php");
include("Common/Cookie.php");
# 应用公共函数文件
include("Common/Log.php"); # 引用日志函数包
include("Common/Debug.php");
include("Common/Public.php"); # 文件操作函数包
# 动态加载文件
include("Common/Entrance.php"); # 引入入口文件包
# 应用结构包调用
include(ROOT."Application/Common/Public.php");
# 框架柱目录文件路径
if(!defined("RING")) define("RING", "Origin".DS);
# 创建基础常量
# 默认应用访问目录，默认为空，当进行web开发时，区分前后台时，填入并在Apply下建立同名文件夹
if(!defined("__APPLICATION__")) define("__APPLICATION__", Configuration("DEFAULT_APPLICATION"));
# 插件应用常量
if(!defined("__PLUGIN__")) define("__PLUGIN__", Configuration("ROOT_PLUGIN"));
# 资源应用常量
if(!defined("__RESOURCE__")) define("__RESOURCE__", Configuration("ROOT_RESOURCE"));
// 调用方法体
Entrance();
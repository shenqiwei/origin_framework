<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 */
# 初始公共函数包
include("import.php");
include("config.php");
include("initialize.php");
# 基础操作方法包应用
include("request.php");
include("validate.php");
include("session.php");
include("cookie.php");
# 应用公共函数文件
include("log.php"); # 引用日志函数包
include("public.php"); # 文件操作函数包
# 应用结构包调用
include(ROOT."application/common/public.php");
# 框架柱目录文件路径
if(!defined("RING")) define("RING", "Origin".DS);
# 创建基础常量
# 默认应用访问目录，默认为空，当进行web开发时，区分前后台时，填入并在Apply下建立同名文件夹
if(!defined("__APPLICATION__")) define("__APPLICATION__", Configuration("DEFAULT_APPLICATION"));
# 插件应用常量
if(!defined("__PLUGIN__")) define("__PLUGIN__", Configuration("ROOT_PLUGIN"));
# 资源应用常量
if(!defined("__RESOURCE__")) define("__RESOURCE__", Configuration("ROOT_RESOURCE"));
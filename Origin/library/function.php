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
include("exception.php");
# 基础操作方法包应用
include("request.php");
include("validate.php");
include("session.php");
include("cookie.php");
# 应用公共函数文件
include("log.php"); # 引用日志函数包
include("public.php"); # 文件操作函数包
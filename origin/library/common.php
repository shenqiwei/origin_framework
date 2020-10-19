<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 */
# 初始公共函数包
include("config.php");
include("log.php"); # 引用日志函数包
include("initialize.php");
include("exception.php");
# 基础操作方法包应用
include("request.php");
include("validate.php");
/**
 * @access public
 * @param string $uri 文件路径
 * @return string
 * @context 文件地址链接符转化
*/
function replace($uri)
{
    return str_replace(RE_DS,DS,$uri);
}
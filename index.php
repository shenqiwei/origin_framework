<?php
/**
 * coding: utf-8  *
 * system OS: windows2008  *
 * work Tools:Phpstorm  *
 * language Ver: php7.1  *
 * agreement: PSR-1 to PSR-11  *
 * filename: IoC.index *
 * version: 0.1  *
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @since 0.1
 * @copyright 2015-2017
 * @context:
 * IoC 单向入口操作文件
*/
# 设置默认访问
define('ROOT_INDEX','index');
# 设置调试状态
define('DEBUG',true);
# 设置错误提示
define('ERROR',false);
# 代码重加载
define('MARK_RELOAD',false);
# 调用通道入口文件
include('Origin/Door.php');
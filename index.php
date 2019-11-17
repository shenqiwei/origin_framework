<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.5
 * @copyright 2015-2019
 * @context:
 * IoC 单向入口操作文件
*/
# 设置调试状态
define('DEBUG',true);
# 设置错误提示
define('ERROR',false);
# 代码重加载
define('MARK_RELOAD',false);
# 调用默认访问
define('DEFAULT_VISIT',false);
# 使用空元素补充
define('USE_EMPTY',false);
# 调用通道入口文件
include('Origin/Door.php');
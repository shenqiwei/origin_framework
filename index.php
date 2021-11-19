<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 1.0
 * @copyright 2015-2019
 * @context:
 * Origin框架单向入口操作文件
*/
# 设置调试状态
const DEBUG = true;
# 设置加载时间显示状态
const TIME = false;
# 设置错误提示
const ERROR = false;
# 代码重加载
const MARK_RELOAD = false;
# 默认访问应用目录
const DEFAULT_APPLICATION = 'home';
# 默认访问类名称
const DEFAULT_CLASS = 'index';
# 默认访问方法名称
const DEFAULT_FUNCTION = 'index';
# 调用通道入口文件
include('origin/point.php');
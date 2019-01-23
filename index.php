<?php
/**
-*- coding: utf-8 -*-
-*- system OS: windows2008 -*-
-*- work Tools:Phpstorm -*-
-*- language Ver: php7.1 -*-
-*- agreement: PSR-1 to PSR-11 -*-
-*- filename: IoC.index-*-
-*- version: 0.1 -*-
-*- structure: common framework -*-
-*- designer: 沈启威 -*-
-*- developer: 沈启威 -*-
-*- partner: 沈启威 -*-
-*- chinese Context:
-*- IoC 单向入口操作文件
*/
# 设置默认访问
define('ROOT_INDEX','index');
# 编码重编译
define('MARK_RELOAD',FALSE);
# 设置调试状态
define('DEBUG',FALSE);
# 设置错误提示
define('ERROR',TRUE);
# 行为链接日志
define('ACTION_LOG',TRUE);
# 请求链接日志
define('ACCESS_LOG',TRUE);
# 数据链接日志
define('CONNECT_LOG',TRUE);
# 错误日志
define('ERROR_LOG',TRUE);
# 调用通道入口文件
include('Origin/Door.php');
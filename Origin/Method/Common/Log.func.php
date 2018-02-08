<?php
/**
-*- coding: utf-8 -*-
-*- system OS: windows2008 -*-
-*- work Tools:Phpstorm -*-
-*- language Ver: php7.1 -*-
-*- agreement: PSR-1 to PSR-11 -*-
-*- filename: IoC.Origin.Function.function-*-
-*- version: 0.1-*-
-*- structure: common framework -*-
-*- designer: 沈启威 -*-
-*- developer: 沈启威 -*-
-*- partner: 沈启威 , 任彦明 -*-
-*- chinese Context:
-*- create Time: 2017/01/09 15:34
-*- update Time: 2017/01/09 15:34
-*- IoC 日志操作包
 */
/**
 * @access public
 * @param string $uri 日志路径
 * @param string $msg 日志模板
 * @param boolean $found 是否创建日志文件
 * @return  boolean
 * @content 日志写入
 */
function write($uri,$msg,$found=true)
{
    $_files = new \Origin\Kernel\File\File();
    # 操作类型变量,当需要补全结构时
    $_operate = ($found === true)?'fw':'w';
    # 调用结构验证方法
    return $_files->write($uri, $_operate,$msg);
}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function daoLogs($msg)
{
    # 数据库操作日志
    $_uri = 'Logs/Connect/'.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg,true);
}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function actionLogs($msg)
{
    # 访问行为日志
    $_uri = 'Logs/Action/'.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg,true);
}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function accessLogs($msg)
{
    # 链接记录日志
    $_uri = 'Logs/Access/'.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg,true);
}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function errorLogs($msg)
{
    # 异常记录日志
    $_uri = 'Logs/Error/'.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg,true);
}
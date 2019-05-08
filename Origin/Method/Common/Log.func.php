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
    $_uri = Config('ROOT_LOG').Config('LOG_CONNECT').date('Ymd').Config('LOG_SUFFIX');
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
    $_uri = Config('ROOT_LOG').Config('LOG_OPERATE').date('Ymd').Config('LOG_SUFFIX');
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
    $_uri =Config('ROOT_LOG').Config('LOG_ACCESS').date('Ymd').Config('LOG_SUFFIX');
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
    $_uri = Config('ROOT_LOG').Config('LOG_EXCEPTION').date('Ymd').Config('LOG_SUFFIX');
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg,true);
}
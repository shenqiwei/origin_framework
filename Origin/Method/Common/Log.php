<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 */
/**
 * @access public
 * @param string $uri 日志路径
 * @param string $msg 日志模板
 * @return  boolean
 * @content 日志写入
 */
function write($uri,$msg)
{
    $_files = new Origin\Kernel\File\File();
    # 调用结构验证方法
    return $_files->write($uri, "fw",$msg);
}
/**
 * 数据库操作日志 statement log
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function sLog($msg)
{
    $_uri = Config('ROOT_LOG').Config('LOG_CONNECT').date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg);
}
/**
 * 异常记录日志 error log
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function eLog($msg)
{
    $_uri = Config('ROOT_LOG').Config('LOG_EXCEPTION').date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg);
}
/**
 * 异常记录日志 exception log
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function iLog($msg)
{
    $_uri = Config('ROOT_LOG').Config('LOG_INITIALIZE').'initialize.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg);
}
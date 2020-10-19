<?php
/**
 * @author 沈起葳 <cheerup.shen@foxmail.com>
 * @version 0.1
 * @copyright 2015-2017
 */
/**
 * @access public
 * @param string $folder 日志路径
 * @param string $context 日志模板
 * @return  boolean
 * @content 日志写入
 */
function write($folder,$context)
{
    $_receipt = false;
    logWrite:
    # 使用写入方式进行日志创建创建和写入
    $_handle = fopen(ROOT.replace($folder),"a");
    if($_handle){
        # 执行写入操作，并返回操作回执
        $_receipt = fwrite($_handle,$context);
        # 关闭文件源
        fclose($_handle);
    }else{
        if(!file_exists($folder)){
            $_dir = substr($folder,0,strrpos($folder,"/"));
            $_dir = explode("/",$_dir);
            $_new = null;
            for($_i = 0;$_i < count($_dir);$_i++){
                $_new .= DS.$_dir[$_i];
                if(!is_dir(ROOT.$_new)){
                    mkdir(ROOT.$_new,0777);
                }
            }
            goto logWrite;
        }
    }
    return $_receipt;
}
/**
 * 数据库操作日志 statement log
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function sLog($msg)
{
    $_uri = LOG_CONNECT.date('Ymd').'.log';
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
    $_uri = LOG_EXCEPTION.date('Ymd').'.log';
    $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
    return write($_uri,$_model_msg);
}
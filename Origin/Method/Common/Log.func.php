<?php
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
    if(DEBUG or CONNECT_LOG){
        # 数据库操作日志
        $_uri = Configurate('ROOT_LOG').Configurate('LOG_CONNECT').date('Ymd').Configurate('LOG_SUFFIX');
        $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
        return write($_uri,$_model_msg,true);
    }else{
        return null;
    }
}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function actionLogs($msg)
{
    if(DEBUG or ACTION_LOG){
        # 访问行为日志
        $_uri = Configurate('ROOT_LOG').Configurate('LOG_OPERATE').date('Ymd').Configurate('LOG_SUFFIX');
        $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
        return write($_uri,$_model_msg,true);
    }else{
        return null;
    }

}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function accessLogs($msg)
{
    if(DEBUG or ACCESS_LOG){
        # 链接记录日志
        $_uri = Configurate('ROOT_LOG').Configurate('LOG_ACCESS').date('Ymd').Configurate('LOG_SUFFIX');
        $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
        return write($_uri,$_model_msg,true);
    }else{
        return null;
    }

}
/**
 * @access public
 * @param string $msg 日志模板信息
 * @return mixed
 */
function errorLogs($msg)
{
    if(DEBUG or ERROR_LOG){
        # 异常记录日志
        $_uri = Configurate('ROOT_LOG').Configurate('LOG_EXCEPTION').date('Ymd').Configurate('LOG_SUFFIX');
        $_model_msg = date("Y/m/d H:i:s")." [Note]: ".$msg.PHP_EOL;
        return write($_uri,$_model_msg,true);
    }else{
        return null;
    }

}